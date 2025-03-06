<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave_types extends Model
{
    protected $fillable = [
        'name',
        'reduces_annual_leave',
    ];

    // Relasi One-to-Many: Pengajuan cuti yang menggunakan tipe ini
    public function leaves()
    {
        return $this->hasMany(Leaves::class, 'leave_types_id', 'id');
    }
}
