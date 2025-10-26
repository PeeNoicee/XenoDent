from flask import Flask, request, jsonify

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
        # Basic validation only
        if not request.json:
            return jsonify({"success": False, "error": "No JSON data provided"}), 400

        has_image = 'image' in request.json if request.json else False

        return jsonify({
            "success": True,
            "message": "Basic request processing works",
            "has_image": has_image,
            "json_keys": list(request.json.keys()) if request.json else []
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
