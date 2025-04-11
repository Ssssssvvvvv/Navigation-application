from flask import Flask, request, jsonify, render_template
import numpy as np
from PIL import Image
import tensorflow as tf
import cv2
import pillow_heif
import time

pillow_heif.register_heif_opener()

app = Flask(__name__)
model = tf.keras.models.load_model('mnist_model_retrained.h5')

# Настройки отладки
DEBUG_IMAGES = False
DEBUG_PREFIX = f"debug_{int(time.time())}"


def save_debug_image(image, stage_name):
    """Сохранение отладочных изображений"""
    if DEBUG_IMAGES:
        filename = f"{DEBUG_PREFIX}_{stage_name}.jpg"
        if len(image.shape) == 2:
            image = cv2.cvtColor(image, cv2.COLOR_GRAY2BGR)
        cv2.imwrite(filename, image)


def find_table(image):
    """Поиск квадратной таблички с цифрами"""
    # Конвертация в градации серого
    gray = cv2.cvtColor(image, cv2.COLOR_RGB2GRAY)
    save_debug_image(gray, "01_gray")

    # Размытие и бинаризация
    blurred = cv2.GaussianBlur(gray, (7, 7), 0)
    thresh = cv2.adaptiveThreshold(blurred, 255,
                                   cv2.ADAPTIVE_THRESH_GAUSSIAN_C,
                                   cv2.THRESH_BINARY_INV, 21, 10)
    save_debug_image(thresh, "02_threshold")

    # Морфологическая обработка
    kernel = cv2.getStructuringElement(cv2.MORPH_RECT, (15, 15))
    closed = cv2.morphologyEx(thresh, cv2.MORPH_CLOSE, kernel)
    save_debug_image(closed, "03_morphology")

    # Поиск контуров
    contours, _ = cv2.findContours(closed, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
    contours = sorted(contours, key=cv2.contourArea, reverse=True)[:3]

    # Визуализация контуров
    contour_img = cv2.cvtColor(closed, cv2.COLOR_GRAY2BGR)
    cv2.drawContours(contour_img, contours, -1, (0, 255, 0), 2)
    save_debug_image(contour_img, "04_all_contours")

    for cnt in contours:
        # Аппроксимация контура
        peri = cv2.arcLength(cnt, True)
        approx = cv2.approxPolyDP(cnt, 0.02 * peri, True)

        # Параметры фильтрации
        x, y, w, h = cv2.boundingRect(cnt)
        aspect_ratio = w / float(h)
        area = cv2.contourArea(cnt)

        # Условия для таблички
        if (len(approx) == 4 and
                0.3 <= aspect_ratio <= 3 and
                w > 100 and h > 100 and
                area > 5000):

            # Проверка наличия цифр внутри
            roi = gray[y:y + h, x:x + w]
            _, digit_thresh = cv2.threshold(roi, 127, 255, cv2.THRESH_BINARY_INV)
            digit_contours, _ = cv2.findContours(digit_thresh, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)

            if len(digit_contours) >= 2:
                table_img = cv2.cvtColor(gray, cv2.COLOR_GRAY2BGR)
                cv2.rectangle(table_img, (x, y), (x + w, y + h), (0, 0, 255), 3)
                save_debug_image(table_img, "05_table_found")
                return (x, y, w, h)

    raise ValueError("Table not found")


def preprocess_image(img):
    """Предобработка изображения"""
    img_array = np.array(img)
    save_debug_image(img_array, "00_original")

    # Обрезка таблички
    x, y, w, h = find_table(img_array)
    cropped = img_array[y:y + h, x:x + w]
    save_debug_image(cropped, "06_cropped_table")

    # Обработка цифр
    gray = cv2.cvtColor(cropped, cv2.COLOR_RGB2GRAY)
    blurred = cv2.GaussianBlur(gray, (3, 3), 0)
    thresh = cv2.adaptiveThreshold(blurred, 255,
                                   cv2.ADAPTIVE_THRESH_GAUSSIAN_C,
                                   cv2.THRESH_BINARY_INV, 23, 5)
    save_debug_image(thresh, "07_digits_threshold")

    # Очистка шумов
    kernel = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (3, 3))
    cleaned = cv2.morphologyEx(thresh, cv2.MORPH_CLOSE, kernel, iterations=2)
    cleaned = cv2.morphologyEx(cleaned, cv2.MORPH_OPEN, np.ones((3, 3)), iterations=2)

    save_debug_image(cleaned, "08_cleaned_digits")

    return cleaned

def find_digits(img):
    """Поиск контуров с улучшенными критериями"""
    contours, _ = cv2.findContours(img, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_TC89_L1)

    digit_contours = []
    for cnt in contours:
        x, y, w, h = cv2.boundingRect(cnt)
        area = cv2.contourArea(cnt)
        perimeter = cv2.arcLength(cnt, True)

        # Обновленные параметры:
        aspect_ratio = w / max(h, 1e-5)
        solidity = area / (w * h + 1e-5)
        compactness = (4 * np.pi * area) / (perimeter ** 2 + 1e-5)

        # Условия фильтрации:
        if (10000 < area < 15000 and
                0.5 < aspect_ratio and
                solidity > 0.01 and
                compactness > 0):

            # Расширение BBox только на 5%
            pad = int(min(w, h) * 0.05)
            x = max(0, x - pad)
            y = max(0, y - pad)
            w = min(img.shape[1] - x, w + 2 * pad)
            h = min(img.shape[0] - y, h + 2 * pad)

            digit_contours.append((x, y, w, h))

    debug_img = cv2.cvtColor(img, cv2.COLOR_GRAY2BGR)

    for (x, y, w, h) in digit_contours:
        # Рисование прямоугольников
        cv2.rectangle(debug_img,
                      (x, y),
                      (x + w, y + h),
                      (0, 255, 0),
                      2)

        cv2.putText(debug_img,
                    f"{w}x{h}",
                    (x, y - 5),
                    cv2.FONT_HERSHEY_SIMPLEX,
                    0.5,
                    (0, 0, 255),
                    1)

    # Сохранение отладочного изображения
    save_debug_image(debug_img, "10_digit_bboxes")

    # Сортировка по X и площади
    digit_contours.sort(key=lambda c: (c[0], -c[2] * c[3]))
    return digit_contours[:3]


def classify_digit(digit_img, model):
    """Классификация с продвинутым центрированием"""
    # Автоматическое кадрирование
    contours, _ = cv2.findContours(digit_img, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
    if contours:
        cnt = max(contours, key=cv2.contourArea)
        x, y, w, h = cv2.boundingRect(cnt)
        digit_img = digit_img[y:y + h, x:x + w]

    # Добавление границ для сохранения пропорций
    digit_img = cv2.copyMakeBorder(digit_img, 10, 10, 10, 10,
                                   cv2.BORDER_CONSTANT, value=0)

    # Центрирование с учетом моментов
    resized = cv2.resize(digit_img, (20, 20))
    moments = cv2.moments(resized)
    cx = int(moments["m10"] / moments["m00"]) if moments["m00"] != 0 else 10
    cy = int(moments["m01"] / moments["m00"]) if moments["m00"] != 0 else 10

    # Смещение в центр
    M = np.float32([[1, 0, 14 - cx], [0, 1, 14 - cy]])
    centered = cv2.warpAffine(resized, M, (28, 28), flags=cv2.INTER_AREA)

    # Улучшенная нормализация
    centered = cv2.GaussianBlur(centered, (3, 3), 0)
    centered = (centered / 255.0).astype("float32")
    centered = np.expand_dims(centered, axis=(0, -1))

    return str(np.argmax(model.predict(centered)))


@app.route('/')
def upload_form():
    return render_template('index.html')


@app.route('/predict', methods=['POST'])
def predict():
    try:
        file = request.files['image']
        if not file or file.filename == '':
            return jsonify({'error': 'No image uploaded'}), 400

        # Обработка изображения
        pil_img = Image.open(file).convert('RGB')
        processed = preprocess_image(pil_img)
        digits = find_digits(processed)

        if len(digits) != 3:
            return jsonify({'error': f'Found {len(digits)} digits'}), 400

        # Распознавание цифр
        predictions = []
        for i, (x, y, w, h) in enumerate(digits):
            digit_roi = processed[y:y + h, x:x + w]
            predictions.append(classify_digit(digit_roi, model))
            save_debug_image(digit_roi, f"09_digit_{i}")

        return jsonify({'number': int(''.join(predictions))})

    except Exception as e:
        return jsonify({'error': str(e)}), 500


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5050)