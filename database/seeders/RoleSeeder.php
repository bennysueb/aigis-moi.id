<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Livewire\Admin\Role\Index as RoleIndex;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // --- PERMISSIONS ---
        // Menggunakan firstOrCreate agar seeder bisa dijalankan ulang tanpa error
        Permission::firstOrCreate(['name' => 'manage pages']);
        Permission::firstOrCreate(['name' => 'manage users']);
        Permission::firstOrCreate(['name' => 'manage events']);
        Permission::firstOrCreate(['name' => 'manage media']);
        Permission::firstOrCreate(['name' => 'manage roles_permissions']);
        Permission::firstOrCreate(['name' => 'checkin attendees']);
        Permission::firstOrCreate(['name' => 'manage forms']);
        Permission::firstOrCreate(['name' => 'manage news']);
        Permission::firstOrCreate(['name' => 'manage categories']);
        Permission::firstOrCreate(['name' => 'manage application settings']);
        Permission::firstOrCreate(['name' => 'manage section templates']);
        Permission::firstOrCreate(['name' => 'manage menus']);
        Permission::firstOrCreate(['name' => 'manage welcome']);
        Permission::firstOrCreate(['name' => 'manage broadcasts']);
        Permission::firstOrCreate(['name' => 'send global broadcasts']);
        Permission::firstOrCreate(['name' => 'manage social wall']);

        // --- Izin khusus untuk Exhibitor ---
        Permission::firstOrCreate(['name' => 'manage own profile/booth']);
        Permission::firstOrCreate(['name' => 'view registrant list']);
        Permission::firstOrCreate(['name' => 'chat with attendees']);
        Permission::firstOrCreate(['name' => 'export registrant data']);
        Permission::firstOrCreate(['name' => 'use qr scanner']);

        // --- Izin khusus untuk Tenant ---
        Permission::firstOrCreate(['name' => 'manage tenant users']);
        Permission::firstOrCreate(['name' => 'manage tenant settings']);
        Permission::firstOrCreate(['name' => 'manage tenant profile']);
        Permission::firstOrCreate(['name' => 'manage products']);
        Permission::firstOrCreate(['name' => 'manage orders']);
        Permission::firstOrCreate(['name' => 'view sales report']);


        // --- ROLE: Super Admin ---
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->syncPermissions(Permission::all());

        // --- ROLE: Administrator ---
        $adminRole = Role::firstOrCreate(['name' => 'Administrator']);
        $adminRole->syncPermissions(Permission::all());

        // --- ROLE: Event Manager ---
        $eventManagerRole = Role::firstOrCreate(['name' => 'Event Manager']);
        $eventManagerRole->syncPermissions([
            'manage pages',
            'manage events',
            'manage media',
            'manage broadcasts'
        ]);

        // --- ROLE: Article Manager ---
        $articleManagerRole = Role::firstOrCreate(['name' => 'Article Manager']);
        $articleManagerRole->syncPermissions([
            'manage news',
            'manage categories',
            'manage social wall'
        ]);

        // --- ROLE: Exhibitor ---
        $exhibitorRole = Role::firstOrCreate(['name' => 'Exhibitor']);
        $exhibitorRole->syncPermissions([
            'manage own profile/booth',
            'view registrant list',
            'chat with attendees',
            'export registrant data',
            'use qr scanner'
        ]);

        // --- ROLE: Tenant ---
        $tenantRole = Role::firstOrCreate(['name' => 'Tenant']);
        $tenantRole->syncPermissions([
            'manage products',
            'manage orders',
            'view sales report',
            'manage tenant profile',
        ]);
    }
}
