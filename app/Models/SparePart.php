<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SparePart extends Model
{
    protected $fillable = [
        'name',
        'quantity',
        'description'
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];
} 