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
        'notes'
    ];

    public function xrays()
    {
        return $this->hasMany(xrays::class, 'patient_id');
    }
}
