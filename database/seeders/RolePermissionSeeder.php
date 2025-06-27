<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Recipe permissions
            'recipe.view',
            'recipe.create',
            'recipe.edit',
            'recipe.delete',
            'recipe.approve',
            'recipe.reject',
            
            // Category permissions
            'category.view',
            'category.create',
            'category.edit',
            'category.delete',
            
            // Tag permissions
            'tag.view',
            'tag.create',
            'tag.edit',
            'tag.delete',
            
            // User permissions
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            'user.ban',
            
            // Article permissions
            'article.view',
            'article.create',
            'article.edit',
            'article.delete',
            'article.publish',
            
            // Collection permissions
            'collection.view',
            'collection.create',
            'collection.edit',
            'collection.delete',
            
            // Rating permissions
            'rating.create',
            'rating.edit',
            'rating.delete',
            
            // System permissions
            'system.settings',
            'system.backup',
            'system.logs',
            'system.analytics',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo([
            'recipe.view',
            'recipe.create',
            'recipe.edit',
            'collection.view',
            'collection.create',
            'collection.edit',
            'collection.delete',
            'rating.create',
            'rating.edit',
        ]);

        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo([
            'recipe.view',
            'recipe.create',
            'recipe.edit',
            'recipe.approve',
            'recipe.reject',
            'category.view',
            'category.create',
            'category.edit',
            'tag.view',
            'tag.create',
            'tag.edit',
            'user.view',
            'article.view',
            'article.create',
            'article.edit',
            'article.publish',
            'collection.view',
            'collection.create',
            'collection.edit',
            'collection.delete',
            'rating.create',
            'rating.edit',
            'rating.delete',
        ]);

        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Assign roles to existing users
        $adminUser = User::where('email', 'admin@beefood.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('admin');
        }

        $managerUser = User::where('email', 'manager@beefood.com')->first();
        if ($managerUser) {
            $managerUser->assignRole('manager');
        }

        // Assign user role to other users
        $regularUsers = User::whereNotIn('email', ['admin@beefood.com', 'manager@beefood.com'])->get();
        foreach ($regularUsers as $user) {
            $user->assignRole('user');
        }
    }
} 