<?php

namespace App\Services;

use App\Models\Action;
use App\Models\SparePart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

class ActionService
{
    /**
     * Get all actions
     *
     * @return Collection
     */
    public function getAllActions(): Collection
    {
        try {
            return Action::with(['technician', 'sparePart', 'machineReports'])
                ->latest()
                ->get();
        } catch (\Exception $e) {
            Log::error('Error fetching actions: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get action by ID
     *
     * @param int $id
     * @return Action
     */
    public function getActionById(int $id): Action
    {
        try {
            return Action::with(['technician', 'sparePart', 'machineReports'])
                ->findOrFail($id);
        } catch (\Exception $e) {
            Log::error('Error fetching action by ID: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create new action
     *
     * @param array $data
     * @param array $files
     * @return Action
     */
    public function createAction(array $data, $files = null): Action
    {
        DB::beginTransaction();
        try {
            $data['technician_id'] = Auth::id();
            $action = Action::create($data);
            if (!empty($data['spare_part_id']) && !empty($data['quantity'])) {
                $sparePart = SparePart::findOrFail($data['spare_part_id']);
                $sparePart->decrement('quantity', $data['quantity']);
            }
            // Handle images
            if ($files && is_array($files)) {
                foreach ($files as $file) {
                    $path = $file->store('action_images', 'public');
                    $action->images()->create(['file_path' => $path]);
                }
            }
            DB::commit();
            return $action;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update action
     *
     * @param int $id
     * @param array $data
     * @param array $files
     * @return Action
     */
    public function updateAction(int $id, array $data, $files = null): Action
    {
        DB::beginTransaction();
        try {
            $action = Action::findOrFail($id);
            $oldSparePartId = $action->spare_part_id;
            $oldQuantity = $action->quantity;
            $action->update($data);
            if (!empty($data['spare_part_id']) && !empty($data['quantity'])) {
                if ($oldSparePartId && $oldSparePartId != $data['spare_part_id']) {
                    $oldSparePart = SparePart::find($oldSparePartId);
                    if ($oldSparePart) {
                        $oldSparePart->increment('quantity', $oldQuantity);
                    }
                }
                if ($oldSparePartId == $data['spare_part_id'] && $oldQuantity != $data['quantity']) {
                    $sparePart = SparePart::find($data['spare_part_id']);
                    $difference = $oldQuantity - $data['quantity'];
                    if ($difference > 0) {
                        $sparePart->increment('quantity', $difference);
                    } else {
                        $sparePart->decrement('quantity', abs($difference));
                    }
                } else {
                    $sparePart = SparePart::find($data['spare_part_id']);
                    $sparePart->decrement('quantity', $data['quantity']);
                }
            }
            // Handle images
            if ($files && is_array($files)) {
                foreach ($files as $file) {
                    $path = $file->store('action_images', 'public');
                    $action->images()->create(['file_path' => $path]);
                }
            }
            DB::commit();
            return $action;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete action
     *
     * @param int $id
     * @return bool
     */
    public function deleteAction(int $id): bool
    {
        DB::beginTransaction();
        try {
            $action = Action::findOrFail($id);
            
            // Return spare part quantity if exists
            if ($action->spare_part_id && $action->quantity) {
                $sparePart = SparePart::find($action->spare_part_id);
                if ($sparePart) {
                    $sparePart->increment('quantity', $action->quantity);
                }
            }
            
            // Detach from machine reports
            $action->machineReports()->detach();
            
            // Delete the action
            $action->delete();
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getActionsByMachineReport($reportId)
    {
        return Action::with(['technician', 'sparePart'])
            ->whereHas('machineReports', function ($query) use ($reportId) {
                $query->where('machine_reports.id', $reportId);
            })
            ->latest()
            ->get();
    }

    public function getActionsByTechnician($technicianId)
    {
        return Action::with(['sparePart', 'machineReports'])
            ->where('technician_id', $technicianId)
            ->latest()
            ->get();
    }

    public function getActionsByStatus($status)
    {
        return Action::with(['technician', 'sparePart', 'machineReports'])
            ->where('status', $status)
            ->latest()
            ->get();
    }
} 