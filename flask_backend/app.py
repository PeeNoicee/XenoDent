from flask import Flask, request, jsonify

# XenoDent AI Flask Service - Minimal test without CORS

app = Flask(__name__)

@app.route('/')
def index():
    return {"message": "XenoDent AI Flask Service", "status": "running"}

@app.route('/predict', methods=['POST'])
def predict():
    return jsonify({
        "success": True,
        "message": "Flask service is working!",
        "received_data": request.method == "POST"
    })

if __name__ == '__main__':
    import os
    port = int(os.environ.get('PORT', 5000))
    app.run(host='0.0.0.0', port=port)
