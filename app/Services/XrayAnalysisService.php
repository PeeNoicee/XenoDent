<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class XrayAnalysisService
{
    protected $pythonScript;
    protected $pythonPath;

    public function __construct()
    {
        $this->pythonScript = app_path('Services/XrayAnalyzer.py');
        $this->pythonPath = env('PYTHON_PATH', '/opt/venv/bin/python3');
    }

    public function analyzeImage($imagePath)
    {
        try {
            if (!file_exists($imagePath)) {
                throw new \Exception('Image file not found');
            }

            // Read the image file and encode to base64
            $imageData = base64_encode(file_get_contents($imagePath));

            // Flask API endpoint
            $flaskUrl = env('FLASK_API_URL', 'https://xenodent-flask.onrender.com/predict');

            // Prepare the request data
            $postData = json_encode([
                'image' => $imageData
            ]);

            // Initialize cURL
            $ch = curl_init($flaskUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60); // 60 seconds timeout
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For development - remove in production

            // Execute the request
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);

            curl_close($ch);

            if ($curlError) {
                Log::error('Flask API connection error', [
                    'error' => $curlError,
                    'url' => $flaskUrl
                ]);
                throw new \Exception('Unable to connect to Flask API: ' . $curlError);
            }

            if ($httpCode !== 200) {
                Log::error('Flask API returned error', [
                    'http_code' => $httpCode,
                    'response' => $response,
                    'url' => $flaskUrl
                ]);
                // Return a result that indicates API error
                return [
                    'api_error' => true,
                    'error_message' => 'Flask API returned HTTP ' . $httpCode,
                    'response' => $response
                ];
            }

            // Parse the JSON response
            $result = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid JSON response from Flask API', [
                    'response' => $response,
                    'json_error' => json_last_error_msg()
                ]);
                throw new \Exception('Invalid JSON response from Flask API');
            }

            // Check if Flask API returned an error
            if (isset($result['success']) && $result['success'] === false) {
                return [
                    'api_error' => true,
                    'error_message' => $result['error'] ?? 'Flask API returned error',
                    'api_error_details' => $result['api_error_details'] ?? null
                ];
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('X-ray analysis failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'image_path' => $imagePath
            ]);
            throw $e;
        }
    }
} 