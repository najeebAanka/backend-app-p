<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;


class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
     
        
//        $modules = ["users" ,"horses" ,"auctions" ,"lots" ,"blog-posts"];
//        foreach ($modules as $m){
//        Permission::create(['name' => 'create-'.$m]);
//        Permission::create(['name' => 'edit-'.$m]);
//        Permission::create(['name' => 'delete-'.$m]);   
//            
//        }
//        
//    
//
//        $adminRole = Role::create(['name' => 'Admin']);
//        $editorRole = Role::create(['name' => 'Editor']);
//        $sellerRole = Role::create(['name' => 'Seller']);
//
//        
//           foreach ($modules as $m){
//        $adminRole->givePermissionTo([
//            'create-'.$m,
//            'edit-'.$m,
//            'delete-'.$m,
//           
//        ]);
//           }
//
//        $editorRole->givePermissionTo([
//            'create-blog-posts',
//            'edit-blog-posts',
//            'delete-blog-posts',
//        ]);
        
        $user = User::find(32);

        $user->assignRole('Admin');
    }
}
