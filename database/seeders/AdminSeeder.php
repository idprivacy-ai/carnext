<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Dealer;
use Spatie\Permission\Models\Role;

use Spatie\Permission\Models\Permission;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin','guard_name' => 'admin']);
        $seo = Role::firstOrCreate(['name' => 'seo','guard_name' => 'admin']);

        $dealerRole = Role::firstOrCreate(['name' => 'dealer','guard_name' => 'dealer']);
        $seo = Role::where('name', 'seo')->first();
        /*$admin = Admin::create([
                'first_name' =>'Ravi',
                'last_name' =>'Kathait',
                'email' => 'ravikathait01@gmail.com',
                'password' => Hash::make('!Q@W3e4r'),
            ],
        );

        $admin2 = Admin::create([
                'first_name' =>'car',
                'last_name' =>'next',
                'email' => 'admin@carnext.com',
                'password' => Hash::make('!Q@W3e4r'),
            ],
        );*/
       /* $seoadmin = Admin::create([
            'first_name' =>'Seo',
            'last_name' =>'Expert',
            'email' => 'seo@carnext.autos',
            'password' => Hash::make('!Q@W3e4r'),
        ]);*/


        $admin = Admin::find(1);
        $admin->assignRole('admin');
        $seoadmin = Admin::find(2);
        //$admin2->assignRole('admin');
        //$seoadmin->assignRole('seo');

         // Create roles
       
        // Create permissions
       // Admin Permissions
            Permission::firstOrCreate(['name' => 'manage users', 'guard_name' => 'admin']);
            Permission::firstOrCreate(['name' => 'manage dealers', 'guard_name' => 'admin']);
            Permission::firstOrCreate(['name' => 'Manage Demo Request', 'guard_name' => 'admin']);
            Permission::firstOrCreate(['name' => 'manage stores', 'guard_name' => 'admin']);
            Permission::firstOrCreate(['name' => 'manage subscription', 'guard_name' => 'admin']);
            Permission::firstOrCreate(['name' => 'manage roles', 'guard_name' => 'admin']);
            Permission::firstOrCreate(['name' => 'Manage Employee', 'guard_name' => 'admin']);
            Permission::firstOrCreate(['name' => 'manage transaction', 'guard_name' => 'admin']);
            Permission::firstOrCreate(['name' => 'Manage Blog', 'guard_name' => 'admin']);
            Permission::firstOrCreate(['name' => 'Manage Media Page', 'guard_name' => 'admin']);
            Permission::firstOrCreate(['name' => 'manage ads', 'guard_name' => 'admin']);
            Permission::firstOrCreate(['name' => 'manage leads', 'guard_name' => 'admin']);
            Permission::firstOrCreate(['name' => 'Manage Contact Us', 'guard_name' => 'admin']);
            Permission::firstOrCreate(['name' => 'manage anisa', 'guard_name' => 'admin']);
            Permission::firstOrCreate(['name' => 'Manage Website Branding & Social', 'guard_name' => 'admin']);

            // Dealer Permissions
            Permission::firstOrCreate(['name' => 'manage employee', 'guard_name' => 'dealer']);
            Permission::firstOrCreate(['name' => 'manage role', 'guard_name' => 'dealer']);
            Permission::firstOrCreate(['name' => 'manage store', 'guard_name' => 'dealer']);
            Permission::firstOrCreate(['name' => 'manage subscription', 'guard_name' => 'dealer']);
            Permission::firstOrCreate(['name' => 'view lead', 'guard_name' => 'dealer']);
            Permission::firstOrCreate(['name' => 'View Store Vehicles', 'guard_name' => 'dealer']);
            Permission::firstOrCreate(['name' => 'manage payment', 'guard_name' => 'dealer']);
            Permission::firstOrCreate(['name' => 'View Analytics', 'guard_name' => 'dealer']);

            // Assign permissions to roles
            $adminRole->givePermissionTo([
                'manage roles',
                'Manage Employee',
                'manage transaction',
                'manage stores',
                'manage subscription',
                'manage users',
                'manage dealers',
                'Manage Demo Request',
                'Manage Blog',
                'Manage Media Page',
                'manage ads',
                'manage leads',
                'Manage Contact Us',
                'manage anisa',
                'Manage Website Branding & Social'
            ]);

            $seo->givePermissionTo([
                'Manage Blog',
                'Manage Media Page',
                'manage ads'
            ]);


    }
}
