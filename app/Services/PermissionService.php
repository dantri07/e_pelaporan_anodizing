<?php

namespace App\Services;

use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PermissionService
{
    /**
     * Get all permissions with pagination
     *
     * @return LengthAwarePaginator
     */
    public function getAllPermissions(): LengthAwarePaginator
    {
        try {
            return Permission::paginate(10);
        } catch (\Exception $e) {
            Log::error('Error fetching permissions: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get permission by ID
     *
     * @param int $id
     * @return Permission
     */
    public function getPermissionById(int $id): Permission
    {
        try {
            return Permission::findOrFail($id);
        } catch (\Exception $e) {
            Log::error('Error fetching permission by ID: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create new permission
     *
     * @param array $data
     * @return Permission
     */
    public function createPermission(array $data): Permission
    {
        try {
            $permission = Permission::create(['name' => $data['name']]);
            Log::info('Permission created successfully: ' . $permission->name);
            return $permission;
        } catch (\Exception $e) {
            Log::error('Error creating permission: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update permission
     *
     * @param int $id
     * @param array $data
     * @return Permission
     */
    public function updatePermission(int $id, array $data): Permission
    {
        try {
            $permission = Permission::findOrFail($id);
            $permission->update(['name' => $data['name']]);
            Log::info('Permission updated successfully: ' . $permission->name);
            return $permission;
        } catch (\Exception $e) {
            Log::error('Error updating permission: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete permission
     *
     * @param int $id
     * @return bool
     */
    public function deletePermission(int $id): bool
    {
        try {
            $permission = Permission::findOrFail($id);
            $permission->delete();
            Log::info('Permission deleted successfully: ' . $permission->name);
            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting permission: ' . $e->getMessage());
            throw $e;
        }
    }
} 