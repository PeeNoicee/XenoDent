<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\authUser;
use App\Models\xrays;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class XrayControl extends Controller
{
    //
    public function upload(Request $request){

        if (!$request->hasFile('image')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }
    
        $file = $request->file('image');
        $originalName = $file->getClientOriginalName();
        $path = $file->storeAs('xray_images', $originalName, 'public');
    
        $fullPath = storage_path('app/public/' . $path);

    
        $xray = xrays::firstOrCreate([
            'path' => $path,
        ]);
    
        return response()->json([
            'message' => 'X-ray uploaded with metadata',
            'png_path' => Storage::url('xray_images/' . $originalName),
        ]);

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


    public function analyze(){

        
    }
}
