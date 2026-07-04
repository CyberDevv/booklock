<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // If the Spatie permission tables haven't been migrated yet, skip seeding
        $tableNames = config('permission.table_names') ?? [];
        $permissionsTable = $tableNames['permissions'] ?? 'permissions';
        $rolesTable = $tableNames['roles'] ?? 'roles';

        if (!Schema::hasTable($permissionsTable) || !Schema::hasTable($rolesTable)) {
            $this->command?->info('Spatie permission tables not present, skipping RolesPermissionsSeeder.');

            return;
        }

        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissionsside
        $moviesPermissions = [
            'add movies',
            'view movies',
            'edit movies',
            'delete movies',
        ];

        $analyticsPermissions = [
            'view analytics',
        ];

        $permissions = array_merge($moviesPermissions, $analyticsPermissions);

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // create roles and assign permissions
        $superAdminRole = Role::create(['name' => 'Admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        $storeOwnerRole = Role::create(['name' => 'Customer']);
        $storeOwnerRole->givePermissionTo($moviesPermissions + $analyticsPermissions);
    }
}
