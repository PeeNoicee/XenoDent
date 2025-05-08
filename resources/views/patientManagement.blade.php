@extends('layouts.xrayapp')
@section('content')
<div style="max-width: 800px; margin: 0 auto; padding: 2rem; background-color: rgb(17, 24, 39); border-radius: 8px;">
    <h1 style="font-size: 2rem; font-weight: bold; margin-bottom: 1.5rem; color: #F5F5F5;">Patient Management</h1>

    <!-- Check if there are patients -->
    @if($patients->isEmpty())
        <div style="text-align: center; color: #F5F5F5;">
            <p>No patients found. Please add a new patient.</p>
        </div>
    @else
        <div style="overflow-x: auto; background-color: #333; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background-color: rgb(25, 35, 50); color: #F5F5F5;">
                    <tr>
                        <th style="padding: 1rem; text-align: left;">Patient Name</th>
                        <th style="padding: 1rem; text-align: left;">Birth Date</th>
                        <th style="padding: 1rem; text-align: left;">Gender</th>
                        <th style="padding: 1rem; text-align: left;">Contact Number</th>
                        <th style="padding: 1rem; text-align: left;">Email</th>
                        <th style="padding: 1rem; text-align: left;">Actions</th>
                    </tr>
                </thead>
                <tbody style="background-color: rgb(31, 41, 55); color: #F5F5F5;">
                    @foreach($patients as $patient)
                        <tr>
                            <td style="padding: 1rem;">{{ $patient->name }}</td>
                            <td style="padding: 1rem;">{{ $patient->birth_date }}</td>
                            <td style="padding: 1rem;">{{ $patient->gender }}</td>
                            <td style="padding: 1rem;">{{ $patient->contact_number }}</td>
                            <td style="padding: 1rem;">{{ $patient->email }}</td>
                            <td style="padding: 1rem;">
                                <!-- Action buttons (view, edit, delete) can go here -->
                                <a href="{{ route('editPatient', $patient->id) }}" style="color: #3B82F6; text-decoration: none; margin-right: 1rem;">Edit</a>
                                <form action="{{ route('deletePatient', $patient->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="color: #EF4444; background-color: transparent; border: none; cursor: pointer;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Button to Add a New Patient -->
    <div style="margin-top: 2rem; text-align: right;">
        <a href="{{ route('addPatient') }}" style="background-color: #3B82F6; color: white; padding: 0.75rem 1.5rem; border-radius: 0.375rem; text-decoration: none; transition: background-color 0.3s;">
            Add New Patient
        </a>
    </div>
</div>
@endsection
