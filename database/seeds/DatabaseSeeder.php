<?php

use App\Permission;
use App\Role;
use App\User;
use App\UserDetail;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();

        // Create Super Admin Role and Super Admin User
        $role = [
            'name'         => 'superAdmin', 
            'display_name' => 'Super Admin', 
            'description'  => 'Super Admin Role'
        ];
        $role = Role::create($role);
        $permission = Permission::get();
        foreach ($permission as $key => $value) {
            $role->attachPermission($value);
        }
        $user = [
            'fname'    => 'Super', 
            'lname'    => 'Admin', 
            'email'    => 'superadmin@admin.com', 
            'password' => Hash::make('admin12345')
        ];
        $user = User::create($user);
        $user->attachRole($role);

        //Add User Roles
        $other_roles = [
            [
                'name' => 'user', 
                'display_name' => 'User', 
                'description' => 'User Level Role'
            ]
        ];
        
        foreach ($other_roles as $key => $value) {
            Role::create($value);
        }
    }
}
