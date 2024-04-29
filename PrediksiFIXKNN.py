import cv2
import numpy as np
import pandas as pd
from skimage.feature import graycomatrix, graycoprops
from sklearn.neighbors import KNeighborsClassifier
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score, confusion_matrix
import mysql.connector
import os
import time

class FeatureExtractor:
    def extract_texture_features(self, image_path):
        image = cv2.imread(image_path, cv2.IMREAD_GRAYSCALE)
        glcm = graycomatrix(image, [1], [0], symmetric=True, normed=True)
        contrast = graycoprops(glcm, prop='contrast')[0, 0]
        correlation = graycoprops(glcm, prop='correlation')[0, 0]
        energy = graycoprops(glcm, prop='energy')[0, 0]
        homogeneity = graycoprops(glcm, prop='homogeneity')[0, 0]
        return [contrast, correlation, energy, homogeneity]

    def extract_color_features(self, image_path):
        image = cv2.imread(image_path)
        r, g, b = cv2.split(image)
        avg_r = np.mean(r)
        avg_g = np.mean(g)
        avg_b = np.mean(b)
        return [avg_r, avg_g, avg_b]

    def extract_edge_features(self, image_path):
        image = cv2.imread(image_path, cv2.IMREAD_GRAYSCALE)
        edges = cv2.Canny(image, 100, 200)
        edge_percentage = np.sum(edges) / (image.shape[0] * image.shape[1])
        return [edge_percentage]

    def extract_features(self, image_path):
        texture_features = self.extract_texture_features(image_path)
        color_features = self.extract_color_features(image_path)
        edge_features = self.extract_edge_features(image_path)

        # Combine all features into a single array
        features = texture_features + color_features + edge_features

        return features

    def extract_features_and_save_single_image(self, image_path, output_file, knn_model):
        # Menggunakan metode extract_features yang baru ditambahkan
        features = self.extract_features(image_path)

        # Reshape the array to match the input format for prediction
        features = np.array(features).reshape(1, -1)

        # Predict the category using the trained KNN model
        category = knn_model.predict(features)[0]

        columns = ['Contrast', 'Correlation', 'Energy', 'Homogeneity', 'Average R', 'Average G', 'Average B', 'Edge Percentage', 'Kategori jamur']
        data = pd.DataFrame(np.column_stack([features, [category]]), columns=columns)

        if os.path.exists(output_file):
            # Load existing data
            existing_data = pd.read_excel(output_file)
            # Concatenate existing data with new data
            data = pd.concat([existing_data, data], ignore_index=True)

        data.to_excel(output_file, index=False)

        print(f"Prediksi Kategori: {category}")

class MushroomClassifier:
    def __init__(self, feature_extractor):
        self.feature_extractor = feature_extractor
        self.knn_classifier = KNeighborsClassifier(n_neighbors=5)
        self.accuracy = 0
        self.db_connection = None

    def open_database_connection(self):
        self.db_connection = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="jamur"
        )

    def close_database_connection(self):
        if self.db_connection:
            self.db_connection.close()

    def train(self, data_file):
        self.open_database_connection() 

        # Load the feature data from the Excel file
        data = pd.read_excel(data_file)

        # Separate features and labels
        X = data.iloc[:, :-1].values
        y = data.iloc[:, -1].values

        # Split the data into training and testing sets
        X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

        # Train the KNN classifier
        self.knn_classifier.fit(X_train, y_train)

        # Evaluate the model on the test set
        y_pred = self.knn_classifier.predict(X_test)

        # Confusion Matrix
        conf_matrix = confusion_matrix(y_test, y_pred)

        # Calculate Accuracy Manually
        TP = conf_matrix[1, 1]  # True Positives
        TN = conf_matrix[0, 0]  # True Negatives
        FP = conf_matrix[0, 1]  # False Positives
        FN = conf_matrix[1, 0]  # False Negatives

        self.accuracy = ((TP + TN) / (TP + FP + FN + TN)) * 100

    def save_to_database(self, category, model_accuracy):
        try:
            cursor = self.db_connection.cursor()

            # Mengonversi model_accuracy ke float
            model_accuracy = float(model_accuracy)

            # Query SQL untuk menyimpan data ke dalam tabel notifikasi
            sql_insert = "INSERT INTO deteksi (kategori, akurasi) VALUES (%s, %s)"
            values = (category, model_accuracy)

            # Eksekusi query
            cursor.execute(sql_insert, values)

            # Commit perubahan ke database
            self.db_connection.commit()

        except Exception as e:
            print(f"Error while saving to database: {e}")

        finally:
            cursor.close()

    def __del__(self):
        self.close_database_connection()

if __name__ == "__main__":
    feature_extractor = FeatureExtractor()

    mushroom_classifier = MushroomClassifier(feature_extractor)

    data_file = "output_data/output_data.xlsx"
    mushroom_classifier.train(data_file)

    output_file = "output_data/output_data.xlsx"

    while True:
        sample_image_path = 'captured_images/photo.jpg'
        feature_extractor.extract_features_and_save_single_image(sample_image_path, output_file, mushroom_classifier.knn_classifier)

        # Mengambil fitur dari gambar yang baru
        new_image_features = feature_extractor.extract_features(sample_image_path)

        # Menggunakan model KNN untuk memprediksi kategori
        predicted_category = mushroom_classifier.knn_classifier.predict([new_image_features])[0]
        model_accuracy = mushroom_classifier.accuracy

        # Menyimpan hasil ke database
        mushroom_classifier.save_to_database(predicted_category, model_accuracy)

        # print(f"Prediksi kategori: {predicted_category}")
        print(f"Akurasi data test: {model_accuracy:.2f}%")

        time.sleep(30)
