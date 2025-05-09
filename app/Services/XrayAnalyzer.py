import sys
import json
import cv2
import numpy as np
import requests
import base64
from PIL import Image
import os
import time

def encode_image_to_base64(image_path):
    with open(image_path, "rb") as image_file:
        return base64.b64encode(image_file.read()).decode('utf-8')

def analyze_xray(image_path):
    try:
        # Load and preprocess the image
        img = cv2.imread(image_path)
        if img is None:
            return {
                'success': False,
                'error': 'Could not load image'
            }

        # Convert to grayscale
        gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
        
        # Basic image analysis
        analysis = {
            'success': True,
            'image_info': {
                'width': img.shape[1],
                'height': img.shape[0],
                'channels': img.shape[2] if len(img.shape) > 2 else 1
            },
            'analysis': {
                'mean_intensity': float(np.mean(gray)),
                'std_intensity': float(np.std(gray)),
                'min_intensity': float(np.min(gray)),
                'max_intensity': float(np.max(gray))
            }
        }

        # Flask API Analysis
        try:
            # Encode image to base64
            base64_image = encode_image_to_base64(image_path)

            # Flask API endpoint
            flask_api_url = "http://127.0.0.1:5000/predict"  # Using 127.0.0.1 instead of localhost

            # Prepare the request payload
            payload = {
                "image": base64_image
            }

            # Add retry logic for Flask API
            max_retries = 3
            retry_delay = 2  # seconds
            
            for attempt in range(max_retries):
                try:
                    # Make the API call to Flask backend
                    response = requests.post(flask_api_url, json=payload, timeout=30)
                    response.raise_for_status()

                    # Add Flask API analysis results to the response
                    flask_results = response.json()
                    analysis['flask_analysis'] = flask_results
                    break  # Success, exit retry loop
                    
                except requests.exceptions.RequestException as e:
                    if attempt == max_retries - 1:  # Last attempt
                        raise  # Re-raise the last exception
                    print(f"Attempt {attempt + 1} failed: {str(e)}", file=sys.stderr)
                    time.sleep(retry_delay)

        except requests.exceptions.RequestException as api_error:
            # Log API error but don't fail the entire analysis
            analysis['api_error'] = str(api_error)
            print(f"Flask API Analysis failed: {api_error}", file=sys.stderr)

        return analysis

    except Exception as e:
        return {
            'success': False,
            'error': str(e)
        }

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print(json.dumps({
            'success': False,
            'error': 'Invalid number of arguments'
        }))
        sys.exit(1)

    image_path = sys.argv[1]
    result = analyze_xray(image_path)
    print(json.dumps(result)) 