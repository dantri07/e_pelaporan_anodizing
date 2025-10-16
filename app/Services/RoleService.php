<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleService
{
    /**
     * Get all roles with pagination
     *
     * @return LengthAwarePaginator
     */
    public function getAllRoles(): LengthAwarePaginator
    {
        try {
            return Role::with('permissions')->get();
        } catch (\Exception $e) {
            Log::error('Error fetching roles: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get role by ID
     *
     * @param int $id
     * @return Role
     */
    public function getRoleById(int $id): Role
    {
        try {
            return Role::with('permissions')->findOrFail($id);
        } catch (\Exception $e) {
            Log::error('Error fetching role by ID: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create new role
     *
     * @param array $data
     * @return Role
     */
    public function createRole(array $data): Role
    {
        try {
            $role = Role::create(['name' => $data['name']]);
            if (isset($data['permissions'])) {
                $permissionNames = \Spatie\Permission\Models\Permission::whereIn('id', $data['permissions'])->pluck('name')->toArray();
                $role->syncPermissions($permissionNames);
            }
            Log::info('Role created successfully: ' . $role->name);
            return $role;
        } catch (\Exception $e) {
            Log::error('Error creating role: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update role
     *
     * @param int $id
     * @param array $data
     * @return Role
     */
    public function updateRole(int $id, array $data): Role
    {
        try {
            $role = Role::findOrFail($id);
            $role->update(['name' => $data['name']]);
            if (isset($data['permissions'])) {
                $permissionNames = \Spatie\Permission\Models\Permission::whereIn('id', $data['permissions'])->pluck('name')->toArray();
                $role->syncPermissions($permissionNames);
            }
            Log::info('Role updated successfully: ' . $role->name);
            return $role;
        } catch (\Exception $e) {
            Log::error('Error updating role: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete role
     *
     * @param int $id
     * @return bool
     */
    public function deleteRole(int $id): bool
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();
            Log::info('Role deleted successfully: ' . $role->name);
            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting role: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get all permissions
     *
     * @return Collection
     */
    public function getAllPermissions(): Collection
    {
        try {
            return \Spatie\Permission\Models\Permission::all();
        } catch (\Exception $e) {
            Log::error('Error fetching permissions: ' . $e->getMessage());
            throw $e;
        }
    }
} 