from flask import Flask, request, jsonify
from inference_sdk import InferenceHTTPClient
import sys

app = Flask(__name__)

@app.after_request
def add_cors_headers(response):
    response.headers['Access-Control-Allow-Origin'] = '*'
    response.headers['Access-Control-Allow-Methods'] = 'POST, GET, OPTIONS'
    response.headers['Access-Control-Allow-Headers'] = 'Content-Type'
    return response

# Initialize Roboflow API Client
CLIENT = InferenceHTTPClient(
    api_url="https://serverless.roboflow.com",
    api_key="e43qHtrojjqzsab0tgkz"
)

@app.route('/')
def index():
    return {"message": "XenoDent AI Flask Service - Updated with Roboflow API calls (forced redeploy)", "status": "running"}

@app.route('/predict', methods=['POST', 'OPTIONS'])
def predict():
    if request.method == 'OPTIONS':
        return jsonify({"message": "CORS preflight successful"})

    try:
        # Validate request
        if not request.json or 'image' not in request.json:
            return jsonify({"success": False, "error": "No image data provided"}), 400

        # Get base64 image data (already processed by Laravel)
        image_b64 = request.json['image']

        # Call Roboflow API directly
        try:
            result = CLIENT.infer(image_b64, model_id="xenodent-snjmc/18")

            return jsonify({
                "success": True,
                "predictions": result.get("predictions", []),
                "model_used": "xenodent-snjmc/18",
                "inference_id": result.get("inference_id"),
                "processing_time": result.get("time")
            })

        except Exception as api_error:
            print(f"Roboflow API Error: {str(api_error)}", file=sys.stderr)
            return jsonify({
                "success": False,
                "error": f"AI model inference failed: {str(api_error)}"
            }), 500

    except Exception as e:
        return jsonify({
            "success": False,
            "error": str(e),
            "type": type(e).__name__
        }), 500

if __name__ == '__main__':
    import os
    port = int(os.environ.get('PORT', 5000))
    app.run(host='0.0.0.0', port=port)
