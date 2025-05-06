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

        // Only create if the record doesn't exist
        authUser::firstOrCreate(
            ['user_id' => $userId],
            [
                'name' => Auth::user()->name,
                'authenticated' => 0,
                'edited_by' => Auth::user()->name,
            ]
        );

            $set = "Done";
            
        }


        return $set;
    }

    public function home() {
        // Fetch the user's name
        $userName = Auth::user()->name;
    
        // Check if the user is authenticated as a premium user
        $prem = authUser::select()->where('user_id', Auth::user()->id)
        ->where('authenticated', 1)->first();
    
        if (is_null($prem)) {
            // If the user is not premium, greet with the name
            $welcome = "WELCOME " . $userName . "!";
            $prem = 0;
        } else {
            // If the user is premium, greet with the name and indicate they are a premium user
            $welcome = "WELCOME " . $userName . " (PREMIUM USER)!";
        }
    
        return view('Homepage', Compact('welcome', 'prem'));
    }
    

    public function premium(){
        return view('premiumPage');
    }

    // When the user logs in or updates premium status
    public function updateUser()
    {
        // Update or create the 'ai_auth' record for the user
        $customerDetails = authUser::updateOrCreate(
            ['user_id' => Auth::user()->id],  // Unique constraint for user
            [
                'name' => Auth::user()->name,
                'user_id' => Auth::user()->id,
                'authenticated' => 1,  // Set premium status to 1
                'edited_by' => Auth::user()->name,
            ]
        );
    
        // Save premium status in session
        session(['premium' => $customerDetails->authenticated]);
    
        // Return a success message to the frontend
        return response()->json([
            'message' => 'Purchase successful!',
        ]);
    }
    

    

}
