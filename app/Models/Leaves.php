<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leaves extends Model
{
    protected $fillable = [
        'users_id',
        'leave_types_id',
        'leaves_number',
        'start_date',
        'end_date',
        'days',
        'note',
        'status',
    ];
    // Relasi Many-to-One: Pengguna
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    // Relasi Many-to-One: Tipe cuti
    public function leaveType()
    {
        return $this->belongsTo(Leave_types::class, 'leave_types_id', 'id');
    }
}
