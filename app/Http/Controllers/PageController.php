<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\authUser;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{

    public function home() {

        // Fetch the user's name
        $userName = Auth::user()->name;
    
        // Check if the user is premium
        $prem = $this->ifPrem();
    
        if (!is_null($prem) && $prem !== 0) {
            // If premium, include the user's name and indicate they are premium
            $welcome = "Welcome Dr. " . $userName . " (PREMIUM)";
        } else {
            // If not premium, greet the user with their name as a customer
            $welcome = "Welcome Dr. " . $userName . " (CUSTOMER)";
        }
    
        return view('Homepage', Compact('welcome', 'prem'));
    }
    

    public function premium(){
        return view('premiumPage');
    }


    public function xray(){

        $prem = $this->ifPrem();

        return view('xrayPage', Compact('prem'));
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
