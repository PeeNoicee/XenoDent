from flask import Flask, request, jsonify, render_template
from inference_sdk import InferenceHTTPClient
import cv2
import base64
import numpy as np
from flask_cors import CORS

app = Flask(__name__)
CORS(app)  # Enable CORS for cross-origin requests

# Initialize Roboflow API Client
CLIENT = InferenceHTTPClient(
    api_url="https://serverless.roboflow.com",
    api_key="E6WARDv3iZ4kV75PfaR5"
)

# Define a color map for different classes
color_map = {
    "periapical lesion": (128, 0, 128),
    "impacted": (0, 255, 0),
    "caries": (64, 224, 208),
    "deep caries": (0, 36, 238)
}

@app.route('/')
def index():
    return render_template('index.html')  # This renders the HTML page

@app.route('/predict', methods=['POST'])
def predict():
    try:
        # Get the base64 image data from the request
        image_data = request.json.get("image")
        if not image_data:
            return jsonify({"error": "No image provided"}), 400

        # Decode the base64 image
        img_data = base64.b64decode(image_data)
        np_arr = np.frombuffer(img_data, np.uint8)
        image = cv2.imdecode(np_arr, cv2.IMREAD_COLOR)

        if image is None:
            return jsonify({"error": "Invalid image data"}), 400

        # Run inference using the base64-encoded image
        img_base64 = base64.b64encode(cv2.imencode('.png', image)[1]).decode('utf-8')
        result = CLIENT.infer(img_base64, model_id="xenodent_panoramic/6")

        if "predictions" not in result:
            return jsonify({"error": "No predictions found"}), 400

        predictions = result["predictions"]
        filtered_predictions = [pred for pred in predictions if pred["confidence"] >= 0.5]

        # Draw bounding boxes and confidence percentages
        for pred in filtered_predictions:
            x, y, w, h = int(pred["x"]), int(pred["y"]), int(pred["width"]), int(pred["height"])
            conf = pred["confidence"]
            color = color_map.get(pred["class"].lower(), (255, 255, 255))  # Default to white if class not found

            # Calculate bounding box coordinates
            x1, y1, x2, y2 = int(x - w / 2), int(y - h / 2), int(x + w / 2), int(y + h / 2)
            cv2.rectangle(image, (x1, y1), (x2, y2), color, 3)

            # Draw confidence label
            label = f"{conf * 100:.0f}%"
            font_scale, thickness = 1, 2
            label_size = cv2.getTextSize(label, cv2.FONT_HERSHEY_SIMPLEX, font_scale, thickness)[0]
            padding = 5
            background_rect_x1 = x1
            background_rect_y1 = y1 - label_size[1] - padding
            background_rect_x2 = x1 + label_size[0] + 2 * padding
            background_rect_y2 = y1

            # Create a semi-transparent rectangle background
            overlay = image.copy()
            cv2.rectangle(overlay, (background_rect_x1, background_rect_y1), (background_rect_x2, background_rect_y2), color, -1)
            cv2.addWeighted(overlay, 0.7, image, 0.3, 0, image)

            # Place the confidence percentage text
            cv2.putText(image, label, (x1 + padding, y1 - padding), cv2.FONT_HERSHEY_SIMPLEX, font_scale, (0, 0, 0), thickness)

        # Add color legend
        legend_height = 140
        legend_x = image.shape[1] - 300
        legend_y = image.shape[0] - legend_height - 10

        # Draw legend background
        overlay = image.copy()
        cv2.rectangle(overlay, (legend_x, legend_y), (image.shape[1], image.shape[0]), (255, 255, 255), -1)
        cv2.addWeighted(overlay, 0.7, image, 0.3, 0, image)

        # Draw border for the legend
        cv2.rectangle(image, (legend_x, legend_y), (image.shape[1], image.shape[0]), (0, 0, 0), 2)

        # Draw color boxes and labels
        y_offset = legend_y + 10
        for label, color in color_map.items():
            capitalized_label = label.title()
            cv2.rectangle(image, (legend_x + 10, y_offset), (legend_x + 30, y_offset + 20), color, -1)
            
            label_background_x1 = legend_x + 40
            label_background_y1 = y_offset - 10
            label_background_x2 = label_background_x1 + 180
            label_background_y2 = y_offset + 15

            overlay = image.copy()
            cv2.rectangle(overlay, (label_background_x1, label_background_y1), (label_background_x2, label_background_y2), (255, 255, 255), -1)
            cv2.addWeighted(overlay, 0.7, image, 0.3, 0, image)

            cv2.putText(image, capitalized_label, (label_background_x1 + 5, y_offset + 15), cv2.FONT_HERSHEY_SIMPLEX, 0.8, (0, 0, 0), 2)
            y_offset += 30

        # Convert the modified image back to base64
        _, buffer = cv2.imencode('.png', image)
        img_base64 = base64.b64encode(buffer).decode('utf-8')

        return jsonify({
            "success": True,
            "image": img_base64,
            "predictions": filtered_predictions
        })

    except Exception as e:
        return jsonify({
            "success": False,
            "error": str(e)
        }), 500

if __name__ == '__main__':
    app.run(debug=True, host='127.0.0.1', port=5000)
