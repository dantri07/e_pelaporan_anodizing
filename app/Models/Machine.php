<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
    ];

    public function media()
    {
        return $this->hasMany(MachineMedia::class);
    }
} 