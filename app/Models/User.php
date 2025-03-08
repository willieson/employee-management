<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'address',
        'contact',
        'email',
        'password',
        'role',
        'id_superior',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];


    //Set Role
    public function isAdmin()
    {
        return $this->role === 'HRD';
    }



    // Relasi One-to-One: Mendapatkan atasan (superior)
    public function superior()
    {
        return $this->belongsTo(User::class, 'id_superior', 'id');
    }
    // Relasi One-to-One: Mendapatkan bawahan (jika ada)
    public function subordinate()
    {
        return $this->hasMany(User::class, 'id_superior', 'id');
    }

    // Relasi One-to-Many: Pengajuan cuti
    public function leaves()
    {
        return $this->hasMany(Leaves::class, 'users_id', 'id');
    }

    // Relasi One-to-Many: Jatah cuti tahunan
    public function annualLeaves()
    {
        return $this->hasMany(Annual_leaves::class, 'users_id', 'id');
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
