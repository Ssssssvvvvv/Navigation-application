from flask import Flask, request, jsonify
import numpy as np
from PIL import Image
import tensorflow as tf
import os

app = Flask(__name__)
model = tf.keras.models.load_model('mnist_model.h5')  # Загрузка модели

ALLOWED_EXTENSIONS = {'png', 'jpg', 'jpeg'}


def allowed_file(filename):
    return '.' in filename and filename.rsplit('.', 1)[1].lower() in ALLOWED_EXTENSIONS


@app.route('/predict', methods=['POST'])
def predict():
    if 'image' not in request.files:
        return jsonify({'error': 'No image uploaded'}), 400

    file = request.files['image']
    if file.filename == '' or not allowed_file(file.filename):
        return jsonify({'error': 'Invalid file'}), 400

    try:
        # Преобразование изображения
        img = Image.open(file).convert('L')  # Градации серого
        img = img.resize((28, 28))
        img_array = np.array(img).reshape(1, 28, 28, 1).astype('float32') / 255.0

        # Предсказание
        prediction = model.predict(img_array)
        digit = int(np.argmax(prediction))
        return jsonify({'digit': digit})

    except Exception as e:
        return jsonify({'error': str(e)}), 500


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5050)