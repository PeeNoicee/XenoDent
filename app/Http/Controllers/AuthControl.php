<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\authUser;
use Illuminate\Support\Facades\Auth;

class AuthControl extends Controller
{
    //

    public function getLandingPage($id){

        $flag = $this->authorizeUser($id);

        return view('xrayLanding', Compact('id'));
    }

    public function authorizeUser($id){

        $userId = (int) $id;
        $userCheck = authUser::select()->where('user_id', $userId)->first();
        $set = "";

        if(is_null($userCheck)){
            
            $customer = new authUser;
            $customer->name = Auth::user()->name;
            $customer->user_id = Auth::user()->id;
            $customer->authenticated = 0;
            $customer->edited_by = Auth::user()->name;

            $customer->save();

            $set = "First";

        }else{

            $customerDetails = authUser::updateOrCreate(

                ['user_id' => $userId],
                [
                    'name' => Auth::user()->name,
                    'user_id' => Auth::user()->id,
                    'authenticated' => 0,
                    'edited_by' => Auth::user()->name,
                ]
          

            );

            $set = "Done";
            
        }


        return $set;
    }

    public function home(){

        $welcome = "WELCOME CUSTOMER!";
        $prem = authUser::select()->where('user_id', Auth::user()->id)
        ->where('authenticated', 1)->first();

        if(is_null($prem)){

            $welcome = "WELCOME CUSTOMER!";
            $prem = 0;

        }else{

            $welcome = "WELCOME PREMIUM USER!";
        }

        return view('Homepage', Compact('welcome', 'prem'));
    }

    public function premium(){
        return view('premiumPage');
    }

    public function updateUSer(){

        $customerDetails = authUser::updateOrCreate(

            ['user_id' => Auth::user()->id],
            [
                'name' => Auth::user()->name,
                'user_id' => Auth::user()->id,
                'authenticated' => 1,
                'edited_by' => Auth::user()->name,
            ]
      

        );

        
    }

}
