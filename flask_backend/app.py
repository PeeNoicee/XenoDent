from flask import Flask, request, jsonify

# XenoDent AI Flask Service - Basic Flask with manual CORS

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

    return jsonify({
        "success": True,
        "message": "Flask service is working!",
        "received_data": request.method == "POST"
    })

if __name__ == '__main__':
    import os
    port = int(os.environ.get('PORT', 5000))
    app.run(host='0.0.0.0', port=port)
