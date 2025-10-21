<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function displayPatientManagement()
    {
        // Fetch only patients belonging to the current user (dentist)
        $patients = Auth::user()->patients;
    
        // Check if the user is premium using the new method
        $prem = Auth::user()->getPremiumStatus();
        
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

        // Create patient and assign to current user
        $patientData = $request->all();
        $patientData['user_id'] = Auth::id();
        
        Patient::create($patientData);

        return redirect()->route('patientManagement')->with('success', 'Patient added successfully.');
    }

    public function edit($id)
    {
        // Fetch the patient by ID, ensuring it belongs to current user
        $patient = Auth::user()->patients()->findOrFail($id);
    
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
    
        // Find the patient and update their data, ensuring it belongs to current user
        $patient = Auth::user()->patients()->findOrFail($id);
        $patient->update($request->all());
    
        // Redirect back to the patient management page
        return redirect()->route('patientManagement')->with('success', 'Patient updated successfully.');
    }
    

    // Delete patient function (optional)
    public function destroy($id)
    {
        // Find and delete patient, ensuring it belongs to current user
        $patient = Auth::user()->patients()->findOrFail($id);
        $patient->delete();

        return redirect()->route('patientManagement')->with('success', 'Patient deleted successfully.');
    }
}
