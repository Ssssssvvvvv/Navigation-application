import tensorflow as tf
from tensorflow.keras import layers, models
from tensorflow.keras.datasets import mnist
from tensorflow.keras.preprocessing.image import ImageDataGenerator

def create_model(input_shape=(28, 28, 1), num_classes=10):
    model = models.Sequential([
        layers.Input(shape=input_shape),
        layers.Conv2D(32, (3, 3), activation='relu'),
        layers.MaxPooling2D((2, 2)),
        layers.Conv2D(64, (3, 3), activation='relu'),
        layers.MaxPooling2D((2, 2)),
        layers.Flatten(),
        layers.Dense(128, activation='relu'),
        layers.Dense(num_classes, activation='softmax')
    ])
    return model

def train_and_save_model():
    # Загрузка данных
    (train_images, train_labels), (test_images, test_labels) = mnist.load_data()
    train_images = train_images.reshape((60000, 28, 28, 1)).astype('float32') / 255.0
    test_images = test_images.reshape((10000, 28, 28, 1)).astype('float32') / 255.0

    # Аугментация данных
    train_datagen = ImageDataGenerator(
        rotation_range=10,
        width_shift_range=0.1,
        height_shift_range=0.1
    )

    # Создание и компиляция модели
    model = create_model()
    model.compile(
        optimizer='adam',
        loss='sparse_categorical_crossentropy',
        metrics=['accuracy']
    )

    # Обучение
    model.fit(
        train_datagen.flow(train_images, train_labels, batch_size=32),
        epochs=5,
        validation_data=(test_images, test_labels)
    )

    # Сохранение модели
    model.save('mnist_model.h5')
    print("Модель обучена и сохранена!")

if __name__ == "__main__":
    train_and_save_model()