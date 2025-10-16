<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineReportMedia extends Model
{
    protected $fillable = [
        'machine_report_id',
        'file_path',
        'file_type',
    ];

    public function machineReport()
    {
        return $this->belongsTo(MachineReport::class);
    }
} 