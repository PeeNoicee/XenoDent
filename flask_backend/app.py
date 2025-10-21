from flask import Flask, request, jsonify, render_template
from inference_sdk import InferenceHTTPClient
import cv2
import base64
import numpy as np
from flask_cors import CORS
from tooth_mapper import ToothPositionMapper

app = Flask(__name__)
CORS(app)  # Enable CORS for cross-origin requests

# Initialize Roboflow API Client
CLIENT = InferenceHTTPClient(
    api_url="https://serverless.roboflow.com",
    api_key="E6WARDv3iZ4kV75PfaR5"
)

# Initialize Tooth Position Mapper
tooth_mapper = ToothPositionMapper()

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
        
        # Process predictions with dental position mapping
        enhanced_predictions = tooth_mapper.process_predictions(
            filtered_predictions, 
            image.shape[1],  # image width
            image.shape[0]   # image height
        )

        # Draw bounding boxes with dental position information
        for i, pred in enumerate(filtered_predictions):
            x, y, w, h = int(pred["x"]), int(pred["y"]), int(pred["width"]), int(pred["height"])
            conf = pred["confidence"]
            color = color_map.get(pred["class"].lower(), (255, 255, 255))  # Default to white if class not found

            # Calculate bounding box coordinates
            x1, y1, x2, y2 = int(x - w / 2), int(y - h / 2), int(x + w / 2), int(y + h / 2)
            cv2.rectangle(image, (x1, y1), (x2, y2), color, 3)

            # Get enhanced prediction data
            enhanced_pred = enhanced_predictions[i] if i < len(enhanced_predictions) else None
            
            if enhanced_pred and enhanced_pred.get('dental_location'):
                # Create dental position label
                tooth_num = enhanced_pred['dental_location']['tooth_number']
                quadrant = enhanced_pred['dental_location']['quadrant']
                class_name = enhanced_pred['class'].title()
                
                # Format: "Q: 1 N: 6 D: Caries" (Quadrant: 1, Number: 6, Diagnosis: Caries)
                q_num = enhanced_pred['dental_location']['quadrant_number'] if 'quadrant_number' in enhanced_pred['dental_location'] else str(tooth_num)[0]
                tooth_pos = str(tooth_num)[1] if len(str(tooth_num)) > 1 else str(tooth_num)
                
                label = f"Q: {q_num} N: {tooth_pos} D: {class_name}"
            else:
                # Fallback to confidence percentage
                label = f"{conf * 100:.0f}%"
            
            # Calculate label dimensions and position
            font_scale, thickness = 0.7, 2
            label_size = cv2.getTextSize(label, cv2.FONT_HERSHEY_SIMPLEX, font_scale, thickness)[0]
            padding = 8
            
            # Position label above the bounding box
            background_rect_x1 = x1
            background_rect_y1 = y1 - label_size[1] - padding * 2
            background_rect_x2 = x1 + label_size[0] + padding * 2
            background_rect_y2 = y1
            
            # Ensure label stays within image bounds
            if background_rect_y1 < 0:
                background_rect_y1 = y2
                background_rect_y2 = y2 + label_size[1] + padding * 2

            # Create a semi-transparent rectangle background
            overlay = image.copy()
            cv2.rectangle(overlay, (background_rect_x1, background_rect_y1), (background_rect_x2, background_rect_y2), color, -1)
            cv2.addWeighted(overlay, 0.8, image, 0.2, 0, image)

            # Add border to label background
            cv2.rectangle(image, (background_rect_x1, background_rect_y1), (background_rect_x2, background_rect_y2), (0, 0, 0), 2)

            # Place the dental position text
            text_x = background_rect_x1 + padding
            text_y = background_rect_y1 + label_size[1] + padding
            cv2.putText(image, label, (text_x, text_y), cv2.FONT_HERSHEY_SIMPLEX, font_scale, (0, 0, 0), thickness)

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
            "predictions": enhanced_predictions,
            "raw_predictions": filtered_predictions  # Keep original for debugging
        })

    except Exception as e:
        return jsonify({
            "success": False,
            "error": str(e)
        }), 500

if __name__ == '__main__':
    app.run(debug=True, host='127.0.0.1', port=5000)
