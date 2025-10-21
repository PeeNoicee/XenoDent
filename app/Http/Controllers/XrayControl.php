<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\authUser;
use App\Models\xrays;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\XrayAnalysisService;
use Carbon\Carbon;

class XrayControl extends Controller
{
    protected $xrayAnalysisService;

    public function __construct(XrayAnalysisService $xrayAnalysisService)
    {
        $this->xrayAnalysisService = $xrayAnalysisService;
    }


    public function getXrayCount()
    {
        $dateNow = Carbon::today()->setTimezone('UTC');
        $xrayCount = xrays::where('edited_by', Auth::user()->name)
            ->whereDate('created_at', $dateNow)
            ->whereNotNull('output_image')
            ->count();

        // Get the limit based on authentication status using relationship
        $authUser = Auth::user()->authUser;
        $dentistAuth = $authUser && $authUser->authenticated === 1;

        $limit = config('app.xray.upload_limit');
        if($dentistAuth){
            $limit = config('app.xray.premium_limit');
        }

        return response()->json([
            'count' => $xrayCount,
            'limit' => $limit
        ]);
    }


    //
    public function upload(Request $request){

        $patientID = $request->input('patient_id');

            try {
                Log::info('Upload request received', [
                    'hasFile' => $request->hasFile('image'),
                    'allFiles' => $request->allFiles(),
                    'allInput' => $request->all()
                ]);

                if (!$request->hasFile('image')) {
                    Log::error('No file uploaded');
                    return response()->json(['error' => 'No file uploaded'], 400);
                }
            
                $file = $request->file('image');
                $dentistName = $request->input('dentist_name');
                $patientName = $request->input('patient_name');
                
                // Validate file type
                if (!$file->isValid()) {
                    Log::error('Invalid file upload', [
                        'error' => $file->getError(),
                        'mime' => $file->getMimeType()
                    ]);
                    return response()->json(['error' => 'Invalid file upload: ' . $file->getError()], 400);
                }

                // Validate file is an image
                if (!str_starts_with($file->getMimeType(), 'image/')) {
                    Log::error('Invalid file type', [
                        'mime' => $file->getMimeType()
                    ]);
                    return response()->json(['error' => 'File must be an image'], 400);
                }

                $originalName = $file->getClientOriginalName();
                Log::info('Storing file', [
                    'originalName' => $originalName,
                    'mime' => $file->getMimeType()
                ]);

                $path = $file->storeAs('xray_images', $originalName, 'public');
            
                if (!$path) {
                    Log::error('Failed to store file');
                    return response()->json(['error' => 'Failed to store file'], 500);
                }

                $xray = xrays::firstOrCreate([
                    'patient_id' => $patientID,
                    'path' => $path,
                    'patient_name' => $patientName,
                    'edited_by' => $dentistName,
                ]);

                // Generate the correct URL for the stored file
                $url = asset('storage/' . $path);
                
                Log::info('File uploaded successfully', [
                    'path' => $path,
                    'url' => $url
                ]);
            
                return response()->json([
                    'message' => 'X-ray uploaded with metadata',
                    'png_path' => $url,
                    'storage_path' => $path,
                    'original_name' => $originalName,
                    'image_id' => $xray->id
                ]);
            } catch (\Exception $e) {
                Log::error('Upload failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'error' => 'Upload failed: ' . $e->getMessage()
                ], 500);
            }



        

    }


    public function getImages(){

        // Check if user is premium using relationship
        $authUser = Auth::user()->authUser;
        $isPremium = $authUser && $authUser->authenticated === 1;

        if (!$isPremium) {
            return response()->json([
                'success' => false,
                'error' => 'Gallery access is restricted to premium users only.'
            ], 403);
        }

        $xrays = xrays::all(['id','path']);

        if($xrays->isEmpty()){
            return response()->json([
                'success' => true,
                'images' => [],
                'messages' => 'No Images found.'

            ]);
        }

        $images = $xrays->map(function($xray){
            return storage::url($xray->path);
        });

        return response()->json([

            'success' => true,
            'images' => $images,

        ]);

    }


    /*
    public function analyze(Request $request)
    {

            try {
            // Validate the input
            $request->validate([
                'image_id' => 'required|integer'
            ]);

            // Get the image ID
            $imageId = $request->input('image_id');

            // Retrieve the X-ray record from the database
            $xray = xrays::find($imageId);

            if (!$xray) {
                return response()->json([
                    'success' => false,
                    'error' => 'X-ray record not found'
                ], 404);
            }

            // Construct the full image path
            $fullPath = storage_path('app/public/' . $xray->path);

            if (!file_exists($fullPath)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Image file not found'
                ], 404);
            }

            // Use the service to analyze the image
            $result = $this->xrayAnalysisService->analyzeImage($fullPath);

            // Generate a unique output file name
            $outputFileName = pathinfo($xray->path, PATHINFO_FILENAME) . '_output_' . uniqid() . '.json';
            $outputDirectory = public_path('xrayOutputs');
            
            // Create the output directory if it doesn't exist
            if (!file_exists($outputDirectory)) {
                mkdir($outputDirectory, 0755, true);
            }

            // Save the analysis result as a JSON file
            $outputFilePath = $outputDirectory . '/' . $outputFileName;
            file_put_contents($outputFilePath, json_encode($result, JSON_PRETTY_PRINT));

            // Save the output path to the database
            $xray->output_image = 'xrayOutputs/' . $outputFileName;
            $xray->save();

            return response()->json([
                'success' => true,
                'message' => 'Analysis successful',
                'output_file' => asset('xrayOutputs/' . $outputFileName),
                'result' => $result
            ]);

        } catch (\Exception $e) {
            \Log::error('Analysis failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Analysis failed: ' . $e->getMessage()
            ], 500);
        }




    }
    */

        public function analyze(Request $request)
        {

                $authUser = Auth::user()->authUser;
                $dentistAuth = $authUser && $authUser->authenticated === 1;

                $uploadCount = config('app.xray.upload_limit');

                $dateNow = Carbon::today()->setTimezone('UTC');
                
                $xrayCount = xrays::where('edited_by', Auth::user()->name)
                ->whereDate('created_at', $dateNow)
                ->whereNotNull('output_image')
                ->count();

                if($dentistAuth){

                    $uploadCount = config('app.xray.premium_limit');

                }
                
                if($xrayCount >= $uploadCount){

                      return response()->json(['error' => 'You have reached the limit of uploads'], 400);

                }
     
                else{

                    try {
                        // Validate the input
                        $request->validate([
                            'image_id' => 'required|integer'
                        ]);

                        // Get the image ID
                        $imageId = $request->input('image_id');

                        // Retrieve the X-ray record from the database
                        $xray = xrays::find($imageId);

                        if (!$xray) {
                            return response()->json([
                                'success' => false,
                                'error' => 'X-ray record not found'
                            ], 404);
                        }

                        // Construct the full image path
                        $fullPath = storage_path('app/public/' . $xray->path);

                        if (!file_exists($fullPath)) {
                            return response()->json([
                                'success' => false,
                                'error' => 'Image file not found'
                            ], 404);
                        }

                        // Use the service to analyze the image
                        $result = $this->xrayAnalysisService->analyzeImage($fullPath);

                        // Check if Flask API is running
                        if (isset($result['api_error'])) {
                            return response()->json([
                                'success' => false,
                                'error' => 'Flask server is not running. Please start the Flask backend server and try again.',
                                'flask_error' => true
                            ], 500);
                        }

                        // Check if analysis was successful by verifying flask_analysis exists
                        if (!isset($result['flask_analysis'])) {
                            throw new \Exception('Analysis failed: Undefined array key "flask_analysis"');
                        }

                        // Generate a unique output file name
                        $outputFileName = pathinfo($xray->path, PATHINFO_FILENAME) . '_output_' . uniqid() . '.json';
                        $outputDirectory = public_path('xrayOutputs');
                        
                        // Create the output directory if it doesn't exist
                        if (!file_exists($outputDirectory)) {
                            mkdir($outputDirectory, 0755, true);
                        }

                        // Save the full analysis result including the Base64 image
                        $outputFilePath = $outputDirectory . '/' . $outputFileName;
                        file_put_contents($outputFilePath, json_encode($result, JSON_PRETTY_PRINT));

                        // Only save the output path to the database if analysis was successful
                        $xray->output_image = 'xrayOutputs/' . $outputFileName;
                        $xray->save();

                        // Return the full analysis result, including the Base64 image
                        return response()->json([
                            'success' => true,
                            'message' => 'Analysis successful',
                            'output_file' => asset('xrayOutputs/' . $outputFileName),
                            'result' => $result,
                            'flask_analysis' => $result['flask_analysis'] // Includes the Base64 image
                        ]);

                    } catch (\Exception $e) {
                        Log::error('Analysis failed', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);

                        return response()->json([
                            'success' => false,
                            'error' => 'Analysis failed: ' . $e->getMessage()
                        ], 500);
                    }

                }

        }




}
