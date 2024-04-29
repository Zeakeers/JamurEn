import cv2
import numpy as np
import pandas as pd
import os
from skimage.feature import graycomatrix, graycoprops

class FeatureExtractor:
    def __init__(self, image_folders):
        self.image_folders = image_folders

    def extract_texture_features(self, image_path):
        # Load the image
        image = cv2.imread(image_path, cv2.IMREAD_GRAYSCALE)
        
        # Compute GLCM
        glcm = graycomatrix(image, [1], [0], symmetric=True, normed=True)
        
        # Extract texture properties (contrast, correlation, energy, homogeneity)
        contrast = graycoprops(glcm, prop='contrast')[0, 0]
        correlation = graycoprops(glcm, prop='correlation')[0, 0]
        energy = graycoprops(glcm, prop='energy')[0, 0]
        homogeneity = graycoprops(glcm, prop='homogeneity')[0, 0]
        
        return [contrast, correlation, energy, homogeneity]

    def extract_color_features(self, image_path):
        # Load the image
        image = cv2.imread(image_path)
        
        # Calculate the average value of each channel (R, G, B)
        r, g, b = cv2.split(image)
        avg_r = np.mean(r)
        avg_g = np.mean(g)
        avg_b = np.mean(b)
        
        return [avg_r, avg_g, avg_b]

    def extract_edge_features(self, image_path):
        # Load the image
        image = cv2.imread(image_path, cv2.IMREAD_GRAYSCALE)

        # Apply Canny edge detection
        edges = cv2.Canny(image, 100, 200)

        # Calculate the percentage of edge pixels
        edge_percentage = np.sum(edges) / (image.shape[0] * image.shape[1])

        return [edge_percentage]

    def extract_features_and_save(self, output_folder):
        # Mendapatkan daftar file gambar dalam folder
        feature_data = []

        for folder in self.image_folders:
            image_files = [f for f in os.listdir(folder) if f.endswith('.jpg')]
            for image_file in image_files:
                image_path = os.path.join(folder, image_file)
                texture_features = self.extract_texture_features(image_path)
                color_features = self.extract_color_features(image_path)
                edge_features = self.extract_edge_features(image_path)

                # Menentukan kategori berdasarkan nama folder
                label = 'sehat' if 'sehat' in folder else 'rusak'

                features = texture_features + color_features + edge_features + [label]
                feature_data.append(features)

        # Membuat DataFrame dari fitur-fitur yang diekstraksi
        columns = ['Contrast', 'Correlation', 'Energy', 'Homogeneity', 'Average R', 'Average G', 'Average B', 'Edge Percentage', 'Kategori jamur']
        data = pd.DataFrame(feature_data, columns=columns)

        # Export data ke Excel
        output_file = os.path.join(output_folder, "output_data.xlsx")
        data.to_excel(output_file, index=False)

# Folder gambar
image_folders = ('jamur_sehat', 'jamur_rusak')

# Inisialisasi objek FeatureExtractor
feature_extractor = FeatureExtractor(image_folders)

# Ekstraksi fitur dan simpan data
output_folder = "output_data"
if not os.path.exists(output_folder):
    os.makedirs(output_folder)

feature_extractor.extract_features_and_save(output_folder)
