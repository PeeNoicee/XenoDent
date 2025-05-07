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

    public function authorizeUser($id)
    {
        $userId = (int) $id;
        $userCheck = authUser::select()->where('user_id', $userId)->first();
        $set = "";
    
        if (is_null($userCheck)) {
            // If the user is not in the database, create a new user record with authenticated = 0
            $customer = new authUser;
            $customer->name = Auth::user()->name;
            $customer->user_id = Auth::user()->id;
            $customer->authenticated = 0; // Set as 0 since this is a new user
            $customer->edited_by = Auth::user()->name;
    
            $customer->save();
            $set = "First";
        } else {
            // If the user already exists, update or create the record
            // But only set authenticated to 0 if it's not already set to 1
            if ($userCheck->authenticated !== 1) {
                $customerDetails = authUser::updateOrCreate(
                    ['user_id' => $userId],
                    [
                        'name' => Auth::user()->name,
                        'user_id' => Auth::user()->id,
                        'authenticated' => 0, // Keep as 0 unless it's already a premium user
                        'edited_by' => Auth::user()->name,
                    ]
                );
                $set = "Done";
            } else {
                // If the user is already authenticated as premium, leave it unchanged
                $set = "Already Premium";
            }
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
    

    public function updateUser(Request $request) {
        try {
            // Validate and update user details
            $customerDetails = authUser::updateOrCreate(
                ['user_id' => Auth::user()->id],
                [
                    'name' => Auth::user()->name,
                    'user_id' => Auth::user()->id,
                    'authenticated' => 1,
                    'edited_by' => Auth::user()->name,
                ]
            );
            
            // Return success response or handle further actions
            return response()->json(['message' => 'User updated successfully!']);
        } catch (\Exception $e) {
            // Handle the error gracefully and return an error response
            return response()->json(['error' => 'Failed to update user: ' . $e->getMessage()], 500);
        }
    }
    

}
