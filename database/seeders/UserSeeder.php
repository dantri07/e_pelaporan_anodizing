<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\RolesEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allPermissions = [
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'permission-list',
            'permission-create',
            'permission-edit',
            'permission-delete',
            'spare-part-list',
            'spare-part-create',
            'spare-part-edit',
            'spare-part-delete',
            'action-list',
            'action-create',
            'action-edit',
            'action-delete',
            'machine-report-list',
            'machine-report-create',
            'machine-report-edit',
            'machine-report-delete',
            'machine-list',
            'machine-create',
            'machine-edit',
            'machine-delete',
        ];

        $technicianPermissions = [
            'spare-part-list',
            'machine-report-list',
            'machine-report-create',
            'machine-report-edit',
            'machine-report-delete',
            'action-list',
            'action-create',
            'action-edit',
            'action-delete',
            'machine-list',
        ];

        $operatorPermissions = [
            'spare-part-list',
            'machine-report-list',
            'machine-report-create',
            'machine-report-edit',
            'machine-report-delete',
            'machine-list',
        ];

        // Gunakan firstOrCreate untuk menghindari duplicate saat re-run seeder
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create role admin
        $adminRole = Role::firstOrCreate(['name' => RolesEnum::ADMIN->value]);
        $operatorRole = Role::firstOrCreate(['name' => RolesEnum::OPERATOR->value]);
        $technicianRole = Role::firstOrCreate(['name' => RolesEnum::TECHNICIAN->value]);

        //sync permissions to role admin
        $adminRole->syncPermissions($allPermissions); // Gunakan sync untuk update permissions
        $technicianRole->syncPermissions($technicianPermissions); // Gunakan sync untuk update permissions
        $operatorRole->syncPermissions($operatorPermissions); // Gunakan sync untuk update permissions

        // Create admin user dengan firstOrCreate untuk menghindari duplicate
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'], // Unique identifier
            [
                'name' => 'Admin',
                'username' => 'admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(), // Tambahkan email verified
            ]
        );

        // Assign role (akan skip jika sudah ada)
        if (!$adminUser->hasRole(RolesEnum::ADMIN->value)) {
            $adminUser->assignRole(RolesEnum::ADMIN->value);
        }

        $operatorUser = User::firstOrCreate(
            ['email' => 'operator@example.com'], // Unique identifier
            [
                'name' => 'Operator',
                'username' => 'operator',
                'email' => 'operator@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(), // Tambahkan email verified
            ]
        );

        if (!$operatorUser->hasRole(RolesEnum::OPERATOR->value)) {
            $operatorUser->assignRole(RolesEnum::OPERATOR->value);
        }

        $technicianUser = User::firstOrCreate(
            ['email' => 'technician@example.com'], // Unique identifier
            [
                'name' => 'Technician',
                'username' => 'technician',
                'email' => 'technician@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(), // Tambahkan email verified
            ]
        );

        if (!$technicianUser->hasRole(RolesEnum::TECHNICIAN->value)) {
            $technicianUser->assignRole(RolesEnum::TECHNICIAN->value);
        }

        $this->command->info('Admin user created successfully!');
        $this->command->info('Username: ' . $adminUser->username);
        $this->command->info('Password: password');

        $this->command->info('========================================');

        $this->command->info('Operator user created successfully!');
        $this->command->info('Username: ' . $operatorUser->username);
        $this->command->info('Password: password');

        $this->command->info('========================================');

        $this->command->info('Technician user created successfully!');
        $this->command->info('Username: ' . $technicianUser->username);
        $this->command->info('Password: password');
    }
}