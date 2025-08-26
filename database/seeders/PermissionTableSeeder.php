<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'roleView',
            'roleAdd',
            'roleEdit',
            'roleDelete',
            'userAdd',
            'userView',
            'userDelete',
            'userUpdate',
            'permissionAdd',
            'permissionView',
            'permissionDelete',
            'permissionUpdate',
            'profileView',
            'profileSetting',
            'dashboardView',
         ];
         
         foreach ($permissions as $permission) {
              Permission::create(['name' => $permission]);
         }
    }
}
