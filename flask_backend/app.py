from flask import Flask, request, jsonify, render_template
from inference_sdk import InferenceHTTPClient
import cv2
import base64
import numpy as np
from flask_cors import CORS
from tooth_mapper import ToothPositionMapper
import sys

# XenoDent AI Flask Service - Minimal test version

app = Flask(__name__)
CORS(app)  # Enable CORS for cross-origin requests

# Initialize Roboflow API Client
import os
CLIENT = InferenceHTTPClient(
    api_url="https://serverless.roboflow.com",
    api_key="e43qHtrojjqzsab0tgkz"
)

# Initialize Tooth Position Mapper
tooth_mapper = ToothPositionMapper()

@app.route('/')
def index():
    return {"message": "XenoDent AI Flask Service", "status": "running", "endpoints": ["/predict"]}

@app.route('/predict', methods=['POST'])
def predict():
    try:
        # Get the base64 image data from the request
        image_data = request.json.get("image")
        if not image_data:
            return jsonify({"error": "No image provided"}), 400

        # Simple test response - just return success
        return jsonify({
            "success": True,
            "message": "Flask service is working!",
            "received_image": len(image_data),
            "model_used": "xenodent-snjmc/18"
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
