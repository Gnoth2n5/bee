<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles and permissions
        $this->createRolesAndPermissions();
    }

    private function createRolesAndPermissions()
    {
        // Create permissions
        $permissions = [
            'recipe.view', 'recipe.create', 'recipe.edit', 'recipe.delete',
            'collection.view', 'collection.create', 'collection.edit', 'collection.delete',
            'rating.create', 'rating.edit',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create user role
        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo([
            'recipe.view', 'recipe.create', 'recipe.edit',
            'collection.view', 'collection.create', 'collection.edit', 'collection.delete',
            'rating.create', 'rating.edit',
        ]);

        // Create manager role
        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo(Permission::all());

        // Create admin role
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());
    }

    public function test_user_can_register()
    {
        Livewire::test(Register::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'Password123')
            ->set('password_confirmation', 'Password123')
            ->call('register')
            ->assertRedirect('/');

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => User::where('email', 'test@example.com')->first()->id,
        ]);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $user->assignRole('user');

        Livewire::test(Login::class)
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertRedirect('/');

        $this->assertAuthenticated();
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        Livewire::test(Login::class)
            ->set('email', 'test@example.com')
            ->set('password', 'wrongpassword')
            ->call('login')
            ->assertHasErrors(['email', 'password']);

        $this->assertGuest();
    }

    public function test_registration_validation()
    {
        Livewire::test(Register::class)
            ->set('name', '')
            ->set('email', 'invalid-email')
            ->set('password', '123')
            ->set('password_confirmation', '456')
            ->call('register')
            ->assertHasErrors([
                'name' => 'required',
                'email' => 'email',
                'password' => 'min',
                'password_confirmation' => 'same',
            ]);
    }

    public function test_login_validation()
    {
        Livewire::test(Login::class)
            ->set('email', '')
            ->set('password', '')
            ->call('login')
            ->assertHasErrors([
                'email' => 'required',
                'password' => 'required',
            ]);
    }

    public function test_user_gets_user_role_on_registration()
    {
        Livewire::test(Register::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'Password123')
            ->set('password_confirmation', 'Password123')
            ->call('register');

        $user = User::where('email', 'test@example.com')->first();
        $this->assertTrue($user->hasRole('user'));
    }

    public function test_user_gets_user_role_on_login_if_not_assigned()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // User has no roles initially
        $this->assertFalse($user->hasRole('user'));

        Livewire::test(Login::class)
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->call('login');

        $user->refresh();
        $this->assertTrue($user->hasRole('user'));
    }
}
