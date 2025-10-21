<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $table = 'patients';

    protected $fillable = [
        'name',
        'birth_date',
        'gender',
        'contact_number',
        'email',
        'address',
        'medical_history',
        'allergies',
        'notes',
        'user_id'
    ];

    /**
     * Get the user (dentist) that owns the patient.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the dentist name for this patient.
     */
    public function getDentistName()
    {
        return $this->user ? $this->user->name : 'Unknown';
    }

    /**
     * Get all X-rays for this patient.
     */
    public function xrays()
    {
        return $this->hasMany(xrays::class, 'patient_id');
    }
}
