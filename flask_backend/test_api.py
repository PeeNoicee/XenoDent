#!/usr/bin/env python3

from inference_sdk import InferenceHTTPClient
import base64

# Test Roboflow API directly
def test_roboflow_api():
    print("Testing Roboflow API...")

    # Initialize client with the new API key
    CLIENT = InferenceHTTPClient(
        api_url="https://serverless.roboflow.com",
        api_key="e43qHtrojjqzsab0tgkz"
    )

    # Create a minimal test image (1x1 pixel PNG)
    test_image_b64 = "iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=="

    try:
        print("Testing with COCO model...")
        result = CLIENT.infer(test_image_b64, model_id="coco/1")
        print(f"SUCCESS COCO model: {result}")
    except Exception as e:
        print(f"FAILED COCO model: {e}")

    try:
        print("Testing with dental model...")
        result = CLIENT.infer(test_image_b64, model_id="xenodent-snjmc/18")
        print(f"SUCCESS Dental model: {result}")
    except Exception as e:
        print(f"FAILED Dental model: {e}")

if __name__ == "__main__":
    test_roboflow_api()
