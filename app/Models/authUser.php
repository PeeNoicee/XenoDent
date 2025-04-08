<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class authUser extends Model
{
    //
    use HasFactory;

    protected $table = 'ai_auth';

    protected $fillable = [
        'name',
        'user_id',
        'authenticated',
        'edited_by'
    ];

    
}
