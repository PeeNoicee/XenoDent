<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\authUser;
use App\Models\xrays;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
        $xrayCount = xrays::select()->where('edited_by', Auth::user()->name)
        ->whereDate('created_at', $dateNow)
        ->count();

        return response()->json(['xrayCount' => $xrayCount]);
    }

    //
    public function upload(Request $request){

        $dentistAuth = authUser::select('authenticated')
        ->where('name', Auth::user()->name)
        ->first();

        $uploadCount = 5;

        $dateNow = Carbon::today()->setTimezone('UTC');
        $patientID = $request->input('patient_id');
        
        $xrayCount = xrays::select()->where('edited_by', Auth::user()->name)
        ->whereDate('created_at', $dateNow)
        ->count();

        if($dentistAuth && (int) $dentistAuth->authenticated == 1){

             $uploadCount = 9999;

        }

        
        if($xrayCount >= $uploadCount){

            return response()->json(['error' => 'You have reached the limit of uploads'], 400);

        }else{

            try {
                \Log::info('Upload request received', [
                    'hasFile' => $request->hasFile('image'),
                    'allFiles' => $request->allFiles(),
                    'allInput' => $request->all()
                ]);

                if (!$request->hasFile('image')) {
                    \Log::error('No file uploaded');
                    return response()->json(['error' => 'No file uploaded'], 400);
                }
            
                $file = $request->file('image');
                $dentistName = $request->input('dentist_name');
                $patientName = $request->input('patient_name');
                
                // Validate file type
                if (!$file->isValid()) {
                    \Log::error('Invalid file upload', [
                        'error' => $file->getError(),
                        'mime' => $file->getMimeType()
                    ]);
                    return response()->json(['error' => 'Invalid file upload: ' . $file->getError()], 400);
                }

                // Validate file is an image
                if (!str_starts_with($file->getMimeType(), 'image/')) {
                    \Log::error('Invalid file type', [
                        'mime' => $file->getMimeType()
                    ]);
                    return response()->json(['error' => 'File must be an image'], 400);
                }

                $originalName = $file->getClientOriginalName();
                \Log::info('Storing file', [
                    'originalName' => $originalName,
                    'mime' => $file->getMimeType()
                ]);

                $path = $file->storeAs('xray_images', $originalName, 'public');
            
                if (!$path) {
                    \Log::error('Failed to store file');
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
                
                \Log::info('File uploaded successfully', [
                    'path' => $path,
                    'url' => $url
                ]);
            
                return response()->json([
                    'message' => 'X-ray uploaded with metadata',
                    'png_path' => $url,
                    'storage_path' => $path,
                    'original_name' => $originalName
                ]);
            } catch (\Exception $e) {
                \Log::error('Upload failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'error' => 'Upload failed: ' . $e->getMessage()
                ], 500);
            }



        }

    }


    public function getImages(){

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


    public function analyze(Request $request)
    {
        try {
            $request->validate([
                'image_path' => 'required|string'
            ]);

            $imagePath = $request->input('image_path');
            $fullPath = storage_path('app/public/xray_images/' . $imagePath);

            if (!file_exists($fullPath)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Image file not found'
                ], 404);
            }

            // Use the service to analyze the image
            $result = $this->xrayAnalysisService->analyzeImage($fullPath);
            
            return response()->json($result);

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
}
