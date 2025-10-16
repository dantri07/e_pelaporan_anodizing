<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Action extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'action_id';
    public $incrementing = true;

    protected $fillable = [
        'description',
        'status',
        'date',
        'technician_id',
        'spare_part_id',
        'quantity',
        'notes'
    ];

    protected $casts = [
        'date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function sparePart(): BelongsTo
    {
        return $this->belongsTo(SparePart::class, 'spare_part_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function machineReports(): BelongsToMany
    {
        return $this->belongsToMany(MachineReport::class, 'machine_report_actions', 'action_id', 'machine_report_id')
            ->withTimestamps();
    }

    public function images()
    {
        return $this->hasMany(ActionImage::class, 'action_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByTechnician($query, $technicianId)
    {
        return $query->where('technician_id', $technicianId);
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="badge badge-warning">Pending</span>',
            'in_progress' => '<span class="badge badge-info">In Progress</span>',
            'completed' => '<span class="badge badge-success">Completed</span>',
            default => '<span class="badge badge-secondary">Unknown</span>'
        };
    }

    public function getFormattedDateAttribute()
    {
        return $this->date->format('d M Y H:i');
    }

    public function getCanEditAttribute()
    {
        return auth()->check() && (
            auth()->id() === $this->technician_id || 
            auth()->user()->hasRole('admin')
        );
    }

    public function getCanDeleteAttribute()
    {
        return $this->can_edit;
    }
}
