<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineMedia extends Model
{
    protected $fillable = [
        'machine_id',
        'file_path',
        'file_type',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
} 