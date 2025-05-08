@extends('layouts.xrayapp')

@section('content')
<div style="width: 80%; margin: auto; padding: 20px; background-color: rgb(17, 24, 39); color: white; border-radius: 8px;">
    <h2 style="font-size: 28px; margin-bottom: 20px;">Edit Patient</h2>

    <form action="{{ route('updatePatient', $patient->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div style="margin-bottom: 16px;">
            <label for="name" style="display: block;">Name</label>
            <input type="text" name="name" id="name" value="{{ $patient->name }}" required style="width: 100%; padding: 10px; border-radius: 4px; border: none; background-color: #F9FAFB; color: black;">
        </div>
        <div style="margin-bottom: 16px;">
            <label for="birth_date" style="display: block;">Birth Date</label>
            <input type="date" name="birth_date" id="birth_date" value="{{ $patient->birth_date }}" style="width: 100%; padding: 10px; border-radius: 4px; border: none; background-color: #F9FAFB; color: black;">
        </div>
        <div style="margin-bottom: 16px;">
            <label for="gender" style="display: block;">Gender</label>
            <select name="gender" id="gender" style="width: 100%; padding: 10px; border-radius: 4px; border: none; background-color: #F9FAFB; color: black;">
                <option value="Male" {{ $patient->gender == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ $patient->gender == 'Female' ? 'selected' : '' }}>Female</option>
                <option value="Other" {{ $patient->gender == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>
        <div style="margin-bottom: 16px;">
            <label for="contact_number" style="display: block;">Contact Number</label>
            <input type="text" name="contact_number" id="contact_number" value="{{ $patient->contact_number }}" style="width: 100%; padding: 10px; border-radius: 4px; border: none; background-color: #F9FAFB; color: black;">
        </div>
        <div style="margin-bottom: 16px;">
            <label for="email" style="display: block;">Email</label>
            <input type="email" name="email" id="email" value="{{ $patient->email }}" style="width: 100%; padding: 10px; border-radius: 4px; border: none; background-color: #F9FAFB; color: black;">
        </div>
        <div style="margin-bottom: 16px;">
            <label for="address" style="display: block;">Address</label>
            <textarea name="address" id="address" style="width: 100%; padding: 10px; border-radius: 4px; border: none; background-color: #F9FAFB; color: black;">{{ $patient->address }}</textarea>
        </div>
        <div style="margin-bottom: 16px;">
            <label for="medical_history" style="display: block;">Medical History</label>
            <textarea name="medical_history" id="medical_history" style="width: 100%; padding: 10px; border-radius: 4px; border: none; background-color: #F9FAFB; color: black;">{{ $patient->medical_history }}</textarea>
        </div>
        <div style="margin-bottom: 16px;">
            <label for="allergies" style="display: block;">Allergies</label>
            <textarea name="allergies" id="allergies" style="width: 100%; padding: 10px; border-radius: 4px; border: none; background-color: #F9FAFB; color: black;">{{ $patient->allergies }}</textarea>
        </div>
        <div style="margin-bottom: 16px;">
            <label for="notes" style="display: block;">Notes</label>
            <textarea name="notes" id="notes" style="width: 100%; padding: 10px; border-radius: 4px; border: none; background-color: #F9FAFB; color: black;">{{ $patient->notes }}</textarea>
        </div>
        <button type="submit" style="background-color: rgb(59, 130, 246); color: white; padding: 10px 20px; border-radius: 4px; border: none; cursor: pointer;">Update Patient</button>
    </form>
</div>
@endsection
