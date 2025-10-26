from flask import Flask, request, jsonify, render_template
from inference_sdk import InferenceHTTPClient
import cv2
import base64
import numpy as np
from flask_cors import CORS
from tooth_mapper import ToothPositionMapper

# XenoDent AI Flask Service - Updated with better error handling

app = Flask(__name__)
CORS(app)  # Enable CORS for cross-origin requests

# Initialize Roboflow API Client
import os
CLIENT = InferenceHTTPClient(
    api_url="https://serverless.roboflow.com",
    api_key=os.environ.get("ROBOFLOW_API_KEY", "E6WARDv3iZ4kV75PfaR5")
)

# Initialize Tooth Position Mapper
tooth_mapper = ToothPositionMapper()

# Define a color map for dental classes
color_map = {
    "periapical lesion": (128, 0, 128),  # Purple
    "impacted": (0, 255, 0),           # Green
    "caries": (64, 224, 208),          # Turquoise
    "deep caries": (0, 36, 238),       # Blue
    "tooth": (255, 255, 0),            # Yellow
    "normal": (192, 192, 192)          # Gray
}

@app.route('/')
def index():
    return {"message": "XenoDent AI Flask Service", "status": "running", "endpoints": ["/predict"]}  # API response instead of template

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
        try:
            img_base64 = base64.b64encode(cv2.imencode('.png', image)[1]).decode('utf-8')
            result = CLIENT.infer(img_base64, model_id="xenodent_panoramic/6")
        except Exception as api_error:
            print(f"Roboflow API Error: {str(api_error)}", file=sys.stderr)
            return jsonify({
                "success": False,
                "error": f"AI model inference failed: {str(api_error)}",
                "api_error_details": str(api_error)
            }), 500

        if "predictions" not in result:
            return jsonify({"error": "No predictions found"}), 400

        predictions = result["predictions"]
        filtered_predictions = [pred for pred in predictions if pred["confidence"] >= 0.3]  # Lower threshold for dental

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
                # Dental position label
                tooth_num = enhanced_pred['dental_location']['tooth_number']
                quadrant = enhanced_pred['dental_location']['quadrant']
                class_name = enhanced_pred['class'].title()

                # Format: "Q: 1 N: 6 D: Caries" (Quadrant: 1, Number: 6, Diagnosis: Caries)
                q_num = enhanced_pred['dental_location']['quadrant_number'] if 'quadrant_number' in enhanced_pred['dental_location'] else str(tooth_num)[0]
                tooth_pos = str(tooth_num)[1] if len(str(tooth_num)) > 1 else str(tooth_num)

                label = f"Q: {q_num} N: {tooth_pos} D: {class_name}"
            else:
                # General object detection label
                class_name = pred["class"].title()
                label = f"{class_name}: {conf * 100:.0f}%"

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

        # Convert the modified image back to base64
        _, buffer = cv2.imencode('.png', image)
        img_base64 = base64.b64encode(buffer).decode('utf-8')

        return jsonify({
            "success": True,
            "image": img_base64,
            "predictions": enhanced_predictions,
            "raw_predictions": filtered_predictions,  # Keep original for debugging
            "model_used": "xenodent_panoramic/6",
            "note": "Using dental AI model with position mapping"
        })

    except Exception as e:
        return jsonify({
            "success": False,
            "error": str(e)
        }), 500

if __name__ == '__main__':
    import os
    port = int(os.environ.get('PORT', 5000))
    debug = os.environ.get('FLASK_ENV') != 'production'
    print(f"Starting Flask app on 0.0.0.0:{port}")
    app.run(debug=debug, host='0.0.0.0', port=port)
