<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Annual_leaves extends Model
{
    protected $fillable = [
        'users_id',
        'year',
        'total_leave',
        'remaining_leave',
    ];

    // Relasi Many-to-One: Pengguna
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    // Relasi Many-to-One: Aturan kedaluwarsa
    public function expiredLeave()
    {
        return $this->belongsTo(expired_leaves::class, 'year', 'year');
    }
}
