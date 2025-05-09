<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;


class xrays extends Model
{
    use HasFactory;

    protected $table = 'ai_xray';     

    protected $fillable = [
        'patient_id',
        'patient_name',
        'path',
        'measurement_mm',
        'edited_by'
    ]; 


    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    
    
}
