<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\authUser;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use App\Models\xrays;
use Carbon\Carbon;

class PageController extends Controller
{

    public function home() {
        // Fetch the user's name
        $userName = Auth::user()->name;
    
        // Use consistent premium check method
        $prem = $this->ifPrem();
    
        if ($prem == 0) {
            // If the user is not premium
            $welcome = "WELCOME " . $userName . "!";
        } else {
            // If the user is premium
            $welcome = "WELCOME " . $userName . " (PREMIUM USER)!";
        }
    
        // Pass the $prem variable to the view
        return view('Homepage', Compact('welcome', 'prem'));
    }
    
    

    public function premium(){
        return view('premiumPage');
    }
    


    public function xray(){

        $prem = $this->ifPrem();
        $dentistName = Auth::user()->name;
        $listOfUsers = Auth::user()->patients;

        $dateNow = Carbon::today()->setTimezone('UTC');

        $xrayCount = xrays::where('edited_by', Auth::user()->name)
        ->whereDate('created_at', $dateNow)
        ->whereNotNull('output_image')
        ->count();

        // Get the upload limit based on authentication status
        $uploadLimit = config('app.xray.upload_limit');
        if($prem == 1){
            $uploadLimit = config('app.xray.premium_limit');
        }

        return view('xrayPage', Compact('prem', 'listOfUsers','dentistName', 'xrayCount', 'uploadLimit'));
    }


    public function ifPrem(){

        // Use relationship for better performance
        $authUser = Auth::user()->authUser;
        
        if(is_null($authUser) || $authUser->authenticated !== 1){   
            return 0;
        }

        return 1;
    }


    public function about() {
        return view('about');
    }

    public function howToUse() {
        return view('how-to-use');
    }

}
