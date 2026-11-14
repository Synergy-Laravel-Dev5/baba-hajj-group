<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            ['name' => 'meal_plan_view', 'guard_name' => 'web', 'group_name' => 'Meal Plan'],
            ['name' => 'meal_plan_create', 'guard_name' => 'web', 'group_name' => 'Meal Plan'],
            ['name' => 'meal_plan_edit', 'guard_name' => 'web', 'group_name' => 'Meal Plan'],
            ['name' => 'meal_plan_trash', 'guard_name' => 'web', 'group_name' => 'Meal Plan'],
            ['name' => 'meal_plan_trash_view', 'guard_name' => 'web', 'group_name' => 'Meal Plan'],
            ['name' => 'meal_plan_restore', 'guard_name' => 'web', 'group_name' => 'Meal Plan'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(
                ['name' => $p['name'], 'guard_name' => $p['guard_name']],
                ['group_name' => $p['group_name']]
            );
        }

        // Grant to Admin role automatically
        $adminRole = Role::where('name', 'Admin')->where('guard_name', 'web')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo(collect($permissions)->pluck('name')->toArray());
        }

        // Also give to any user with id 1 or super-admin role if exists
        $superAdminRole = Role::whereIn('name', ['Super Admin', 'SuperAdmin'])->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo(collect($permissions)->pluck('name')->toArray());
        }

        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $names = [
            'meal_plan_view',
            'meal_plan_create',
            'meal_plan_edit',
            'meal_plan_trash',
            'meal_plan_trash_view',
            'meal_plan_restore',
        ];

        Permission::whereIn('name', $names)->delete();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
