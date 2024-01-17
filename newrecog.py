from PIL import Image
from keras_facenet import FaceNet
import numpy as np
from numpy import asarray, expand_dims
import pickle
import cv2
from keras.models import load_model

HaarCascade = cv2.CascadeClassifier(cv2.samples.findFile(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml'))
# Load the FaceNet model
MyFaceNet = FaceNet()

# Load the FaceNet database
myfile = open("data_DATASET_2.pkl", "rb")
database = pickle.load(myfile)
myfile.close()

# Load the neural network model
model = load_model('neural_network_classifier')

# Open the webcam
cap = cv2.VideoCapture(0)

# Set a threshold for confidence level (adjust as needed)
confidence_threshold = 80

while True:
    # Read a frame from the webcam
    _, gbr1 = cap.read()

    # Detect faces in the frame
    wajah = HaarCascade.detectMultiScale(gbr1, 1.1, 4)

    if len(wajah) > 0:
        x1, y1, width, height = wajah[0]
    else:
        x1, y1, width, height = 1, 1, 10, 10

    x1, y1 = abs(x1), abs(y1)
    x2, y2 = x1 + width, y1 + height

    # Convert BGR to RGB
    gbr = cv2.cvtColor(gbr1, cv2.COLOR_BGR2RGB)
    gbr = Image.fromarray(gbr)
    gbr_array = asarray(gbr)
    
    # Extract the face region and preprocess
    face = gbr_array[y1:y2, x1:x2]
    face = Image.fromarray(face)
    face = face.resize((160, 160))
    face = asarray(face)
    face = expand_dims(face, axis=0)

    # Get the FaceNet embeddings for the face
    signature = MyFaceNet.embeddings(face)

   # Use the neural network model to predict the person
    y_pred = model.predict(signature)

    # Get the predicted class label and confidence
    predicted_class = np.argmax(y_pred)
    confidence = round(y_pred[0][predicted_class] * 100, 2)
    
    # Map the class label to a person's name
    class_names = ["Aizat", "Asmadi", "Atikah", "Awadah", "Farhana", "Hasya", "Khairiyani", "Madi", "Mirza", "Nurin", "Putri", "Syamila", "Syuhaida", "Yasmeen", "Zakwan"]
    identity = class_names[predicted_class]

    if confidence >= confidence_threshold:
        identity = class_names[predicted_class]
    else:
        identity = "Unknown"

    # Display the person's name and confidence level
    text = f"{identity} (Confidence: {confidence}%)"
    cv2.putText(gbr1, text, (100, 100), cv2.FONT_HERSHEY_SIMPLEX, 1, (255, 255, 0), 2, cv2.LINE_AA)
    cv2.rectangle(gbr1, (x1, y1), (x2, y2), (0, 255, 0), 2)

    # Display the frame
    cv2.imshow('Real-Time Face Recognition', gbr1)

    # Check for key presses
    k = cv2.waitKey(1) & 0xFF
    if k == 27:  # Press 'Esc' key to exit
        break
    elif k == ord('q'):  # Press 'q' key to close webcam
        break

# Release the webcam and close all windows
cap.release()
cv2.destroyAllWindows()
