from flask import Flask, request, jsonify
import base64
import numpy as np
import cv2

app = Flask(__name__)

@app.after_request
def add_cors_headers(response):
    response.headers['Access-Control-Allow-Origin'] = '*'
    response.headers['Access-Control-Allow-Methods'] = 'POST, GET, OPTIONS'
    response.headers['Access-Control-Allow-Headers'] = 'Content-Type'
    return response

@app.route('/')
def index():
    return {"message": "XenoDent AI Flask Service", "status": "running"}

@app.route('/predict', methods=['POST', 'OPTIONS'])
def predict():
    if request.method == 'OPTIONS':
        return jsonify({"message": "CORS preflight successful"})

    try:
        # Validate request
        if not request.json or 'image' not in request.json:
            return jsonify({"success": False, "error": "No image data provided"}), 400
        
        # Base64 decode and process image
        try:
            img_data = base64.b64decode(request.json['image'])
            np_arr = np.frombuffer(img_data, np.uint8)
            image = cv2.imdecode(np_arr, cv2.IMREAD_COLOR)
            
            if image is None:
                return jsonify({"success": False, "error": "Invalid image data"}), 400
                
            return jsonify({
                "success": True,
                "message": "Image processed successfully",
                "dimensions": {"height": int(image.shape[0]), "width": int(image.shape[1])},
                "channels": int(image.shape[2]) if len(image.shape) > 2 else 1
            })
            
        except Exception as img_error:
            return jsonify({
                "success": False,
                "error": f"Image processing failed: {str(img_error)}",
                "type": type(img_error).__name__
            }), 400
            
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
