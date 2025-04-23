<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class xrays extends Model
{
    //
    use HasFactory;

    protected $table = 'ai_xray';

    protected $fillable = [
        'patient_name',
        'path',
        'measurement_mm',
        'edited_by'
    ];
}
