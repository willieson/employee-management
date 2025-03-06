<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class expired_leaves extends Model
{
    protected $fillable = [
        'year',
        'expires_at',
    ];

    // Relasi One-to-Many: Jatah cuti tahunan
    public function annualLeaves()
    {
        return $this->hasMany(Annual_leaves::class, 'year', 'year');
    }
}
