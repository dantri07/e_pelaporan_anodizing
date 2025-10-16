<?php

namespace App\Services;

use App\Models\MachineReport;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

class MachineReportService
{
    /**
     * Get all machine reports
     *
     * @return Collection
     */
    public function getAllMachineReports(): Collection
    {
        try {
            return MachineReport::with(['user', 'actions'])->get();
        } catch (\Exception $e) {
            Log::error('Error fetching machine reports: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get machine report by ID
     *
     * @param int $id
     * @return MachineReport
     */
    public function getMachineReportById(int $id): MachineReport
    {
        try {
            return MachineReport::with([
                'user',
                'technician',
                'actions.technician',
                'actions.sparePart'
            ])->findOrFail($id);
        } catch (\Exception $e) {
            Log::error('Error fetching machine report by ID: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create new machine report
     *
     * @param array $data
     * @return MachineReport
     */
    public function createMachineReport(array $data): MachineReport
    {
        try {
            $report = MachineReport::create($data);
            Log::info('Machine report created successfully: ' . $report->id);
            return $report;
        } catch (\Exception $e) {
            Log::error('Error creating machine report: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update machine report
     *
     * @param int $id
     * @param array $data
     * @return MachineReport
     */
    public function updateMachineReport(int $id, array $data): MachineReport
    {
        try {
            $report = MachineReport::findOrFail($id);
            $report->update($data);
            Log::info('Machine report updated successfully: ' . $report->id);
            return $report;
        } catch (\Exception $e) {
            Log::error('Error updating machine report: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete machine report
     *
     * @param int $id
     * @return bool
     */
    public function deleteMachineReport(int $id): bool
    {
        try {
            $report = MachineReport::findOrFail($id);
            $report->delete();
            Log::info('Machine report deleted successfully: ' . $report->id);
            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting machine report: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get machine report detail by ID (with all relations)
     *
     * @param int $id
     * @return MachineReport
     */
    public function getMachineReportDetail(int $id): MachineReport
    {
        try {
            return MachineReport::with([
                'user',
                'technician',
                'actions.technician',
                'actions.sparePart',
                'media'
            ])->findOrFail($id);
        } catch (\Exception $e) {
            \Log::error('Error fetching machine report detail: ' . $e->getMessage());
            throw $e;
        }
    }
} 