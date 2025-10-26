#!/usr/bin/env python3

from inference_sdk import InferenceHTTPClient
import base64
import os

# Test Roboflow API with test_0.png
def test_roboflow_api_with_image():
    print("Testing Roboflow API with test_0.png...")

    # Initialize client with the API key
    CLIENT = InferenceHTTPClient(
        api_url="https://serverless.roboflow.com",
        api_key="e43qHtrojjqzsab0tgkz"
    )

    # Load the test image
    image_path = "test_0.png"  # File is in the same directory as test_api.py

    if not os.path.exists(image_path):
        print(f"ERROR: test_0.png not found at {image_path}")
        return

    try:
        # Read and encode the image
        with open(image_path, "rb") as image_file:
            image_data = image_file.read()
            image_b64 = base64.b64encode(image_data).decode('utf-8')

        print(f"Loaded image: {len(image_data)} bytes")
        print(f"Base64 encoded: {len(image_b64)} characters")

        # Test with dental model xenodent-snjmc/18
        print("\nTesting with dental model xenodent-snjmc/18...")
        result = CLIENT.infer(image_b64, model_id="xenodent-snjmc/18")

        print("SUCCESS! Dental AI results:")
        print(f"Inference ID: {result.get('inference_id')}")
        print(f"Processing time: {result.get('time', 'N/A')} seconds")
        print(f"Image dimensions: {result.get('image', {})}")

        predictions = result.get('predictions', [])
        print(f"Number of detections: {len(predictions)}")

        if predictions:
            print("\nDental detections found:")
            for i, pred in enumerate(predictions, 1):
                print(f"{i}. {pred.get('class', 'Unknown')} - Confidence: {pred.get('confidence', 0)*100:.1f}%")
                print(f"   Location: x={pred.get('x', 0):.1f}, y={pred.get('y', 0):.1f}")
                print(f"   Size: width={pred.get('width', 0):.1f}, height={pred.get('height', 0):.1f}")
        else:
            print("No dental conditions detected in this image.")

        return result

    except Exception as e:
        print(f"FAILED: {str(e)}")
        print(f"Error type: {type(e).__name__}")
        return None

if __name__ == "__main__":
    test_roboflow_api_with_image()
