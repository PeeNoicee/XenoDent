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
        $this->pythonPath = env('PYTHON_PATH', '/opt/venv/bin/python3.10');
    }

    public function analyzeImage($imagePath)
    {
        try {
            if (!file_exists($imagePath)) {
                throw new \Exception('Image file not found');
            }

            if (!file_exists($this->pythonScript)) {
                throw new \Exception('Python script not found');
            }

            // Build the command
            $command = sprintf(
                '%s %s %s',
                escapeshellcmd($this->pythonPath),
                escapeshellarg($this->pythonScript),
                escapeshellarg($imagePath)
            );

            // Execute the command
            $output = [];
            $returnVar = 0;
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                Log::error('Python script execution failed', [
                    'command' => $command,
                    'output' => $output,
                    'returnVar' => $returnVar
                ]);
                throw new \Exception('Python script execution failed');
            }

            // Parse the JSON output
            $result = json_decode(implode("\n", $output), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON output from Python script');
            }

            // Note: Tooth position mapping is now handled in the Flask backend
            // No additional processing needed here as Flask already returns enhanced predictions

            return $result;

        } catch (\Exception $e) {
            Log::error('X-ray analysis failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
} 