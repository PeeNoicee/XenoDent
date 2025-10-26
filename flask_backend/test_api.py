#!/usr/bin/env python3

from inference_sdk import InferenceHTTPClient
import base64

# Test Roboflow API directly
def test_roboflow_api():
    print("Testing Roboflow API...")

    # Initialize client with the API key from render.yaml
    CLIENT = InferenceHTTPClient(
        api_url="https://serverless.roboflow.com",
        api_key="E6WARDv3iZ4kV75PfaR5"
    )

    # Create a minimal test image (1x1 pixel PNG)
    test_image_b64 = "iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=="

    try:
        print("Testing with COCO model...")
        result = CLIENT.infer(test_image_b64, model_id="coco/1")
        print(f"✅ COCO model success: {result}")
    except Exception as e:
        print(f"❌ COCO model failed: {e}")

    try:
        print("Testing with dental model...")
        result = CLIENT.infer(test_image_b64, model_id="xenodent_panoramic/6")
        print(f"✅ Dental model success: {result}")
    except Exception as e:
        print(f"❌ Dental model failed: {e}")

if __name__ == "__main__":
    test_roboflow_api()
