<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActionImage extends Model
{
    protected $fillable = [
        'action_id',
        'file_path',
    ];

    public function action()
    {
        return $this->belongsTo(Action::class, 'action_id');
    }
} 