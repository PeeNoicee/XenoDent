<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;

class PatientController extends Controller
{
    public function displayPatientManagement()
    {
        // Fetch all patients from the database
        $patients = Patient::all();
    
        // Get the current logged-in user
        $user = auth()->user();
    
        // Check if the user is premium
        $prem = $user && $user->authenticated == 1; // Assuming 'authenticated' field signifies premium status
        
    
        // Pass the data to the view
        return view('patientManagement', compact('prem', 'patients'));
    }
    

    // Create a function for the add patient page (optional)
    public function create()
    {
        return view('addPatient');
    }

    // Store the new patient in the database (optional)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|string',
            'contact_number' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Patient::create($request->all());

        return redirect()->route('patientManagement')->with('success', 'Patient added successfully.');
    }

    public function edit($id)
    {
        // Fetch the patient by ID
        $patient = Patient::findOrFail($id);
    
        // Pass the patient data to the view
        return view('editPatient', compact('patient'));
    }
    
    public function update(Request $request, $id)
    {
        // Validate the input
        $request->validate([
            'name' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:Male,Female,Other',
            'contact_number' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
    
        // Find the patient and update their data
        $patient = Patient::findOrFail($id);
        $patient->update($request->all());
    
        // Redirect back to the patient management page
        return redirect()->route('patientManagement')->with('success', 'Patient updated successfully.');
    }
    

    // Delete patient function (optional)
    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();

        return redirect()->route('patientManagement')->with('success', 'Patient deleted successfully.');
    }
}
