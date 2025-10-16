<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MachineReport extends Model
{
    protected $fillable = [
        'user_id',
        'machine_name',
        'report_description',
        'report_date',
        'technician_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actions(): BelongsToMany
    {
        return $this->belongsToMany(
            Action::class,
            'machine_report_actions',
            'machine_report_id',
            'action_id'
        )->withTimestamps();
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function media()
    {
        return $this->hasMany(MachineReportMedia::class);
    }
} 