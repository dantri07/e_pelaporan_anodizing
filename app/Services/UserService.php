<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * Get all users with pagination
     *
     * @return LengthAwarePaginator
     */
    public function getAllUsers(): LengthAwarePaginator
    {
        try {
            return User::with('roles')->get();
        } catch (\Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get user by ID
     *
     * @param int $id
     * @return User
     */
    public function getUserById(int $id): User
    {
        try {
            return User::with('roles')->findOrFail($id);
        } catch (\Exception $e) {
            Log::error('Error fetching user by ID: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create new user
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        try {
            $user = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            if (isset($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            Log::info('User created successfully: ' . $user->username);
            return $user;
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update user
     *
     * @param int $id
     * @param array $data
     * @return User
     */
    public function updateUser(int $id, array $data): User
    {
        try {
            $user = User::findOrFail($id);
            
            $updateData = [
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
            ];

            if (isset($data['password']) && !empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $user->update($updateData);

            if (isset($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            Log::info('User updated successfully: ' . $user->username);
            return $user;
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete user
     *
     * @param int $id
     * @return bool
     */
    public function deleteUser(int $id): bool
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            Log::info('User deleted successfully: ' . $user->username);
            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get all roles
     *
     * @return Collection
     */
    public function getAllRoles(): Collection
    {
        try {
            return \Spatie\Permission\Models\Role::all();
        } catch (\Exception $e) {
            Log::error('Error fetching roles: ' . $e->getMessage());
            throw $e;
        }
    }
} 