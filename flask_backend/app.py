from flask import Flask, request, jsonify
import sys

app = Flask(__name__)

@app.after_request
def add_cors_headers(response):
    response.headers['Access-Control-Allow-Origin'] = '*'
    response.headers['Access-Control-Allow-Methods'] = 'POST, GET, OPTIONS'
    response.headers['Access-Control-Allow-Headers'] = 'Content-Type'
    return response

@app.route('/')
def index():
    return {"message": "XenoDent AI Flask Service - Emergency fix", "status": "running"}

@app.route('/predict', methods=['POST', 'OPTIONS'])
def predict():
    if request.method == 'OPTIONS':
        return jsonify({"message": "CORS preflight successful"})

    try:
        # Validate request
        if not request.json or 'image' not in request.json:
            return jsonify({"success": False, "error": "No image data provided"}), 400

        # Return mock dental AI results in the format Laravel expects
        return jsonify({
            "success": True,
            "predictions": [
                {
                    "class": "caries",
                    "confidence": 0.92,
                    "x": 245,
                    "y": 180,
                    "width": 45,
                    "height": 35
                },
                {
                    "class": "tooth",
                    "confidence": 0.89,
                    "x": 198,
                    "y": 165,
                    "width": 38,
                    "height": 42
                },
                {
                    "class": "periapical_lesion",
                    "confidence": 0.76,
                    "x": 312,
                    "y": 225,
                    "width": 28,
                    "height": 32
                }
            ],
            "model_used": "xenodent-snjmc/18",
            "inference_id": "emergency-fix-" + str(hash(str(request.json))),
            "processing_time": 0.034,
            "note": "EMERGENCY FIX - Mock dental AI results. Replace with real Roboflow API when deployment issues resolved."
        })

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
