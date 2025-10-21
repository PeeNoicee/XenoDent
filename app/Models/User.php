<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the auth user record associated with the user.
     */
    public function authUser()
    {
        return $this->hasOne(authUser::class, 'user_id');
    }

    /**
     * Check if the user is premium.
     */
    public function isPremium()
    {
        try {
            $authUser = $this->authUser;
            return $authUser && $authUser->authenticated === 1;
        } catch (\Exception $e) {
            // Log error and return false as fallback
            Log::warning('Error checking premium status for user ' . $this->id . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get premium status as integer (0 or 1) for backward compatibility.
     */
    public function getPremiumStatus()
    {
        return $this->isPremium() ? 1 : 0;
    }

    /**
     * Get all patients belonging to this user (dentist).
     */
    public function patients()
    {
        return $this->hasMany(Patient::class, 'user_id');
    }
}
