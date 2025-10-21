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
        $userCheck = authUser::where('user_id', $userId)->first();
        $set = "";
    
        if (is_null($userCheck)) {
            // If the user is not in the database, create a new user record with authenticated = 0
            try {
                $customer = authUser::create([
                    'name' => Auth::user()->name,
                    'user_id' => Auth::user()->id,
                    'authenticated' => 0,
                    'edited_by' => Auth::user()->name,
                ]);
                $set = "First";
            } catch (\Exception $e) {
                // If creation fails (e.g., due to unique constraint), try to find existing record
                $userCheck = authUser::where('user_id', $userId)->first();
                if ($userCheck) {
                    $set = "Found Existing";
                } else {
                    throw $e; // Re-throw if it's a different error
                }
            }
        } else {
            // If the user already exists, update only if not premium
            if ($userCheck->authenticated !== 1) {
                $userCheck->update([
                    'name' => Auth::user()->name,
                    'edited_by' => Auth::user()->name,
                ]);
                $set = "Updated";
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
            $userId = Auth::user()->id;
            
            // First, try to find existing record
            $existingUser = authUser::where('user_id', $userId)->first();
            
            if ($existingUser) {
                // Update existing record
                $existingUser->update([
                    'name' => Auth::user()->name,
                    'authenticated' => 1,
                    'edited_by' => Auth::user()->name,
                ]);
                $customerDetails = $existingUser;
            } else {
                // Create new record only if none exists
                $customerDetails = authUser::create([
                    'name' => Auth::user()->name,
                    'user_id' => $userId,
                    'authenticated' => 1,
                    'edited_by' => Auth::user()->name,
                ]);
            }
            
            // Return success response or handle further actions
            return response()->json(['message' => 'User updated successfully!']);
        } catch (\Exception $e) {
            // Handle the error gracefully and return an error response
            return response()->json(['error' => 'Failed to update user: ' . $e->getMessage()], 500);
        }
    }
    

}
