import tensorflow as tf
import numpy as np
import os
from PIL import Image


def load_custom_data(data_dir='dataset'):
    images = []
    labels = []

    for filename in os.listdir(data_dir):
        if filename.endswith(('.png', '.jpg', '.jpeg', '.heic')):
            # Предполагаем, что имя файла содержит метку (например: 5_image123.jpg)
            label = int(filename.split('_')[0])

            # Загрузка и предобработка изображения
            img = Image.open(os.path.join(data_dir, filename)).convert('L')
            img = img.resize((28, 28))
            img_array = np.array(img).reshape(28, 28, 1).astype('float32') / 255.0

            images.append(img_array)
            labels.append(label)

    return np.array(images), np.array(labels)


def retrain_model():
    # Загрузка данных
    new_images, new_labels = load_custom_data()
    if len(new_images) == 0:
        print("Ошибка: В папке data нет изображений!")
        return

    # Загрузка модели
    model = tf.keras.models.load_model('mnist_model.h5')

    # Компиляция с меньшим learning rate
    model.compile(
        optimizer=tf.keras.optimizers.Adam(learning_rate=0.0001),
        loss='sparse_categorical_crossentropy',
        metrics=['accuracy']
    )

    # Дообучение
    model.fit(new_images, new_labels, epochs=1, batch_size=32)
    model.save('mnist_model_retrained.h5')
    print("Модель дообучена и сохранена!")


if __name__ == '__main__':
    retrain_model()