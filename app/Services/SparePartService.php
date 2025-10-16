<?php

namespace App\Services;

use App\Models\SparePart;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SparePartService
{
    protected $lowStockThreshold = 5;

    /**
     * Get all spare parts
     *
     * @return Collection
     */
    public function getAllSpareParts(): Collection
    {
        try {
            return SparePart::all();
        } catch (\Exception $e) {
            Log::error('Error fetching spare parts: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get spare part by ID
     *
     * @param int $id
     * @return SparePart
     */
    public function getSparePartById(int $id): SparePart
    {
        try {
            return SparePart::findOrFail($id);
        } catch (\Exception $e) {
            Log::error('Error fetching spare part by ID: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create new spare part
     *
     * @param array $data
     * @return SparePart
     */
    public function createSparePart(array $data): SparePart
    {
        DB::beginTransaction();
        try {
            $sparePart = SparePart::create($data);
            
            // Check if stock is low after creation
            if ($sparePart->quantity <= $this->lowStockThreshold) {
                $this->sendLowStockNotification($sparePart);
            }
            
            Log::info('Spare part created successfully: ' . $sparePart->name);
            DB::commit();
            return $sparePart;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating spare part: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update spare part
     *
     * @param int $id
     * @param array $data
     * @return SparePart
     */
    public function updateSparePart(int $id, array $data): SparePart
    {
        DB::beginTransaction();
        try {
            $sparePart = SparePart::findOrFail($id);
            $oldQuantity = $sparePart->quantity;
            $sparePart->update($data);
            
            // Check if stock is low after update
            if ($sparePart->quantity <= $this->lowStockThreshold && $oldQuantity > $this->lowStockThreshold) {
                $this->sendLowStockNotification($sparePart);
            }
            
            Log::info('Spare part updated successfully: ' . $sparePart->name);
            DB::commit();
            return $sparePart;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating spare part: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete spare part
     *
     * @param int $id
     * @return bool
     */
    public function deleteSparePart(int $id): bool
    {
        try {
            $sparePart = SparePart::findOrFail($id);
            $sparePart->delete();
            Log::info('Spare part deleted successfully: ' . $sparePart->name);
            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting spare part: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get all spare parts with low stock
     *
     * @param int $threshold
     * @return Collection
     */
    public function getLowStockSpareParts(int $threshold = null): Collection
    {
        $threshold = $threshold ?? $this->lowStockThreshold;
        return SparePart::where('quantity', '<=', $threshold)->get();
    }

    /**
     * Send low stock notification to admin users
     *
     * @param SparePart $sparePart
     * @return void
     */
    protected function sendLowStockNotification(SparePart $sparePart): void
    {
        try {
            // Get all admin users
            $adminUsers = User::role('admin')->get();
            
            // Send notification to each admin
            foreach ($adminUsers as $admin) {
                $admin->notify(new LowStockNotification($sparePart));
            }
            
            Log::info('Low stock notification sent for spare part: ' . $sparePart->name);
        } catch (\Exception $e) {
            Log::error('Error sending low stock notification: ' . $e->getMessage());
        }
    }
} 