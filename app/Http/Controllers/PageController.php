<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\authUser;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{

    public function home(){

        $welcome = "WELCOME CUSTOMER!";
        $prem = $this->ifPrem();

        if(!is_null($prem)){
            $welcome = "WELCOME PREMIUM!";
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



}
