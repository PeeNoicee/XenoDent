<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\authUser;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{

    public function home() {
        // Fetch the user's name
        $userName = Auth::user()->name;
    
        // Check if the user is authenticated as a premium user
        $prem = authUser::select()->where('user_id', Auth::user()->id)
            ->where('authenticated', 1)->first();
    
        if (is_null($prem)) {
            // If the user is not premium
            $welcome = "WELCOME " . $userName . "!";
            $prem = 0;
        } else {
            // If the user is premium
            $welcome = "WELCOME " . $userName . " (PREMIUM USER)!";
            $prem = 1;  // Set it to 1 instead of the model object
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
        $listOfUsers = Patient::select()->get();


        return view('xrayPage', Compact('prem', 'listOfUsers','dentistName'));
    }


    public function ifPrem(){

        $prem = authUser::select()->where('user_id', Auth::user()->id)
        ->where('authenticated', 1)->first();

        if(is_null($prem)){   
            $prem = 0;
        }

        return $prem;
    }


    public function about() {
        return view('about');
    }

    public function howToUse() {
        return view('how-to-use');
    }

}
