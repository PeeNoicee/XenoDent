from flask import Flask, request, jsonify
import base64
from PIL import Image
import io

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
            image = Image.open(io.BytesIO(img_data))
            
            return jsonify({
                "success": True,
                "message": "Image processed successfully with PIL",
                "dimensions": {"width": image.width, "height": image.height},
                "format": image.format,
                "mode": image.mode
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
