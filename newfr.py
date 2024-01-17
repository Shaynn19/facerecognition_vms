import os
from os import listdir
from PIL import Image
from numpy import asarray
from numpy import expand_dims
from matplotlib import pyplot
from keras_facenet import FaceNet
from keras.models import load_model
import numpy as np
import pickle
import cv2
from keras.models import Sequential
from keras.layers import Dense, Flatten
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import LabelEncoder, StandardScaler
import matplotlib.pyplot as plt
from sklearn.metrics import confusion_matrix, classification_report
import seaborn as sns
import json

#HaarCascade = cv2.CascadeClassifier('haarcascade_frontalface_default.xml')
HaarCascade = cv2.CascadeClassifier(cv2.samples.findFile(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml'))
MyFaceNet = FaceNet()

dataset_folder='DATASET/'
database = {}

# Loop through each person's folder in the DATASET
for person_folder in os.listdir(dataset_folder):
    person_folder_path = os.path.join(dataset_folder, person_folder)

    # Ensure that it is a directory
    if os.path.isdir(person_folder_path):
        print(f"Processing images for {person_folder}")

        # Loop through each image in the person's folder
        for filename in os.listdir(person_folder_path):
            img_path = os.path.join(person_folder_path, filename)

            # Check if the file is an image
            if filename.lower().endswith(('.png', '.jpg', '.jpeg')):
                gbr1 = cv2.imread(img_path)

                # Check if the image is read successfully
                if gbr1 is not None:
                    wajah = HaarCascade.detectMultiScale(gbr1, 1.1, 4)

                    if len(wajah) > 0:
                        x1, y1, width, height = wajah[0]
                    else:
                        x1, y1, width, height = 1, 1, 10, 10

                    x1, y1 = abs(x1), abs(y1)
                    x2, y2 = x1 + width, y1 + height

                    # Convert BGR to RGB
                    gbr = cv2.cvtColor(gbr1, cv2.COLOR_BGR2RGB)
                    gbr = Image.fromarray(gbr)                  # konversi dari OpenCV ke PIL
                    gbr_array = asarray(gbr)
                    
                    face = gbr_array[y1:y2, x1:x2]                        
                    
                    face = Image.fromarray(face)                       
                    face = face.resize((160,160))
                    face = asarray(face)
                    
                    face = expand_dims(face, axis=0)
                    signature = MyFaceNet.embeddings(face)

                    # Save the FaceNet embedding in the database
                    database[f"{person_folder}_{os.path.splitext(filename)[0]}"] = signature
                else:
                    print(f"Error reading image: {img_path}")
            else:
                print(f"Skipping non-image file: {img_path}")

# Save the FaceNet database
with open("data_DATASET_2.pkl", "wb") as myfile:
    pickle.dump(database, myfile)
myfile.close()

# Extract recognized student names
recognized_students = [key.split('_')[0] for key in database.keys()]

# Export recognized student names to a JSON file
with open("recognized_students.json", "w") as jsonfile:
    json.dump(recognized_students, jsonfile)

# Extract features and labels from the database
features = []
labels = []

for key, value in database.items():
    features.append(value.flatten())  # Flatten the 2D embedding to 1D
    labels.append(key.split('_')[0])  # Extract the person's name

X = np.array(features)
y = np.array(labels)

# Split the data into training and testing sets
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# Normalize features using StandardScaler
scaler = StandardScaler()
X_train = scaler.fit_transform(X_train)
X_test = scaler.transform(X_test)

# Convert labels to numerical values using LabelEncoder
le = LabelEncoder()
y_train_encoded = le.fit_transform(y_train)
y_test_encoded = le.transform(y_test)

# Define the neural network model
model = Sequential()
model.add(Dense(128, activation='relu', input_shape=(X_train.shape[1],)))
model.add(Dense(64, activation='relu'))
model.add(Dense(len(set(y)), activation='softmax'))

model.compile(optimizer='adam', loss='sparse_categorical_crossentropy', metrics=['accuracy'])

# Lists to store training and validation metrics
training_accuracies = []
validation_accuracies = []
training_losses = []
validation_losses = []

# Train the model
for epoch in range(1000):  # Adjust the number of epochs as needed
    history = model.fit(X_train, y_train_encoded, epochs=1, batch_size=32, validation_split=0.2)

    # Get the training and validation accuracy for this epoch
    training_accuracy = history.history['accuracy'][0]
    validation_accuracy = history.history['val_accuracy'][0]
    training_accuracies.append(training_accuracy)
    validation_accuracies.append(validation_accuracy)

    # Get the training and validation loss for this epoch
    training_loss = history.history['loss'][0]
    validation_loss = history.history['val_loss'][0]
    training_losses.append(training_loss)
    validation_losses.append(validation_loss)

    # Print the metrics for this epoch
    print(f'Epoch {epoch + 1}: Training Accuracy = {training_accuracy * 100:.2f}%, '
          f'Validation Accuracy = {validation_accuracy * 100:.2f}%, '
          f'Training Loss = {training_loss:.6f}, Validation Loss = {validation_loss:.6f}')

# Calculate the average training accuracy, validation accuracy, training loss, and validation loss
average_training_accuracy = np.mean(training_accuracies)
average_validation_accuracy = np.mean(validation_accuracies)
average_training_loss = np.mean(training_losses)
average_validation_loss = np.mean(validation_losses)

# Print the average metrics
print(f'Average Training Accuracy: {average_training_accuracy * 100:.2f}%')
print(f'Average Validation Accuracy: {average_validation_accuracy * 100:.2f}%')
print(f'Average Training Loss: {average_training_loss:.6f}')
print(f'Average Validation Loss: {average_validation_loss:.6f}')

# Plot training and validation metrics
plt.figure(figsize=(12, 4))

# Plot training & validation loss values
plt.subplot(1, 2, 1)
plt.plot(training_losses, label='Training Loss')
plt.plot(validation_losses, label='Validation Loss')
plt.legend()
plt.xlabel('Epochs')
plt.ylabel('Loss')

# Plot training & validation accuracy values
plt.subplot(1, 2, 2)
plt.plot(training_accuracies, label='Training Accuracy')
plt.plot(validation_accuracies, label='Validation Accuracy')
plt.legend()
plt.xlabel('Epochs')
plt.ylabel('Accuracy')

plt.show()

# Make predictions on the test data
y_pred = model.predict(X_test)

# Convert predicted probabilities to class labels (argmax)
y_pred_labels = np.argmax(y_pred, axis=1)

# Get unique class labels
class_labels = le.classes_

# Display the confusion matrix
confusion = confusion_matrix(y_test_encoded, y_pred_labels)
print('Confusion Matrix:')
print(confusion)

# Plot the confusion matrix using a heatmap
plt.figure(figsize=(8, 6))
sns.heatmap(confusion, annot=True, fmt='d', cmap='Blues', xticklabels=class_labels, yticklabels=class_labels)
plt.xlabel('Predicted')
plt.ylabel('True')
plt.title('Confusion Matrix')
plt.show()

# Print the classification report
class_report = classification_report(y_test_encoded, y_pred_labels, target_names=class_labels)
print('Classification Report:')
print(class_report)

# Save the trained model
model.save('neural_network_classifier')