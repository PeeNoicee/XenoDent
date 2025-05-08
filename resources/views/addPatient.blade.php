@extends('layouts.xrayapp')

@section('content')
<div style="max-width: 600px; margin: 0 auto; padding: 2rem; background-color:rgb(17, 24, 39); border-radius: 8px;">
    <h1 style="font-size: 2rem; font-weight: bold; margin-bottom: 1.5rem; color: #F5F5F5;">Add New Patient</h1>

    <form action="{{ route('storePatient') }}" method="POST">
        @csrf
        <div style="margin-bottom: 1.5rem;">
            <label for="name" style="display: block; color: #F5F5F5; margin-bottom: 0.5rem;">Name</label>
            <input type="text" name="name" id="name" style="width: 100%; padding: 0.75rem; border: 1px solid #444; border-radius: 0.375rem; color: #000;" required>
        </div>
        
        <div style="margin-bottom: 1.5rem;">
            <label for="birth_date" style="display: block; color: #F5F5F5; margin-bottom: 0.5rem;">Birth Date</label>
            <input type="date" name="birth_date" id="birth_date" style="width: 100%; padding: 0.75rem; border: 1px solid #444; border-radius: 0.375rem; color: #000;" required>
        </div>
        
        <div style="margin-bottom: 1.5rem;">
            <label for="gender" style="display: block; color: #F5F5F5; margin-bottom: 0.5rem;">Gender</label>
            <select name="gender" id="gender" style="width: 100%; padding: 0.75rem; border: 1px solid #444; border-radius: 0.375rem; color: #000;" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
        </div>
        
        <div style="margin-bottom: 1.5rem;">
            <label for="contact_number" style="display: block; color: #F5F5F5; margin-bottom: 0.5rem;">Contact Number</label>
            <input type="text" name="contact_number" id="contact_number" style="width: 100%; padding: 0.75rem; border: 1px solid #444; border-radius: 0.375rem; color: #000;">
        </div>
        
        <div style="margin-bottom: 1.5rem;">
            <label for="email" style="display: block; color: #F5F5F5; margin-bottom: 0.5rem;">Email</label>
            <input type="email" name="email" id="email" style="width: 100%; padding: 0.75rem; border: 1px solid #444; border-radius: 0.375rem; color: #000;">
        </div>
        
        <div style="margin-bottom: 1.5rem;">
            <label for="address" style="display: block; color: #F5F5F5; margin-bottom: 0.5rem;">Address</label>
            <textarea name="address" id="address" style="width: 100%; padding: 0.75rem; border: 1px solid #444; border-radius: 0.375rem; color: #000;"></textarea>
        </div>
        
        <div style="margin-bottom: 1.5rem;">
            <label for="medical_history" style="display: block; color: #F5F5F5; margin-bottom: 0.5rem;">Medical History</label>
            <textarea name="medical_history" id="medical_history" style="width: 100%; padding: 0.75rem; border: 1px solid #444; border-radius: 0.375rem; color: #000;"></textarea>
        </div>
        
        <div style="margin-bottom: 1.5rem;">
            <label for="allergies" style="display: block; color: #F5F5F5; margin-bottom: 0.5rem;">Allergies</label>
            <textarea name="allergies" id="allergies" style="width: 100%; padding: 0.75rem; border: 1px solid #444; border-radius: 0.375rem; color: #000;"></textarea>
        </div>
        
        <div style="margin-bottom: 1.5rem;">
            <label for="notes" style="display: block; color: #F5F5F5; margin-bottom: 0.5rem;">Notes</label>
            <textarea name="notes" id="notes" style="width: 100%; padding: 0.75rem; border: 1px solid #444; border-radius: 0.375rem; color: #000;"></textarea>
        </div>

        <button type="submit" style="background-color: #3B82F6; color: white; padding: 1rem 1.5rem; border-radius: 0.375rem; border: none; cursor: pointer; transition: background-color 0.3s;">
            Save Patient
        </button>
    </form>
</div>
@endsection
