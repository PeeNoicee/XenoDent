from flask import Flask, request, jsonify
from flask_cors import CORS

# XenoDent AI Flask Service - Ultra minimal test

app = Flask(__name__)
CORS(app)

@app.route('/')
def index():
    return {"message": "XenoDent AI Flask Service", "status": "running"}

@app.route('/predict', methods=['POST'])
def predict():
    return jsonify({
        "success": True,
        "message": "Ultra minimal Flask test working!",
        "received_data": bool(request.json)
    })

if __name__ == '__main__':
    import os
    port = int(os.environ.get('PORT', 5000))
    app.run(host='0.0.0.0', port=port)
