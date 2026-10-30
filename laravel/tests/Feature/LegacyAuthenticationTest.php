<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LegacyAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_account_authenticates_using_username_and_hash_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);

        $response = $this->post('/login', ['username' => $user->username, 'password' => 'secret123']);

        $response->assertRedirect('/profile');
        $this->assertAuthenticatedAs($user);
    }

    public function test_locked_account_cannot_authenticate(): void
    {
        $user = User::factory()->create(['status' => 0, 'password' => bcrypt('secret123')]);

        $this->post('/login', ['username' => $user->username, 'password' => 'secret123'])
            ->assertRedirect();
        $this->assertGuest();
    }

    public function test_admin_permissions_are_read_from_role_permissions(): void
    {
        $roleId = DB::table('roles')->insertGetId(['code' => 'admin', 'name' => 'Quản trị viên']);
        $permissionId = DB::table('permissions')->insertGetId(['code' => 'users.manage', 'name' => 'Quản lý tài khoản']);
        DB::table('role_permissions')->insert(['role_id' => $roleId, 'permission_id' => $permissionId]);
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)->get('/users')->assertOk();
        $this->get('/roles')->assertForbidden();
    }

    public function test_users_with_only_account_management_permission_land_on_users_page(): void
    {
        $roleId = DB::table('roles')->insertGetId(['code' => 'account_manager', 'name' => 'Quản lý tài khoản']);
        $permissionId = DB::table('permissions')->insertGetId(['code' => 'users.manage', 'name' => 'Quản lý tài khoản']);
        DB::table('role_permissions')->insert(['role_id' => $roleId, 'permission_id' => $permissionId]);
        $user = User::factory()->create(['role' => 'account_manager']);

        $this->actingAs($user)->get('/')->assertRedirect(route('users.index'));
        $this->post('/logout')->assertRedirect(route('login'));
        $this->post('/login', ['username' => $user->username, 'password' => 'password'])
            ->assertRedirect(route('users.index'));
    }

    public function test_only_user_id_six_has_global_permission_bypass(): void
    {
        $ordinaryAdmin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($ordinaryAdmin)->get('/dashboard')->assertForbidden();

        $superAdmin = User::factory()->create(['id' => 6, 'role' => 'warehouse']);
        $this->actingAs($superAdmin)->get('/dashboard')->assertOk();
    }

    public function test_only_user_id_six_can_assign_the_admin_role(): void
    {
        DB::table('roles')->insert([
            ['code' => 'admin', 'name' => 'Quản trị viên'],
            ['code' => 'warehouse', 'name' => 'Nhân viên kho'],
        ]);
        $permissionId = DB::table('permissions')->insertGetId(['code' => 'users.manage', 'name' => 'Quản lý tài khoản']);
        $adminRoleId = DB::table('roles')->where('code', 'admin')->value('id');
        DB::table('role_permissions')->insert(['role_id' => $adminRoleId, 'permission_id' => $permissionId]);
        $ordinaryAdmin = User::factory()->create(['role' => 'admin']);
        $payload = [
            'username' => 'new-admin', 'password' => 'secret123', 'fullname' => 'Admin mới',
            'role' => 'admin', 'status' => 1,
        ];

        $this->actingAs($ordinaryAdmin)->post('/users', $payload)->assertForbidden();
        $this->actingAs(User::factory()->create(['id' => 6]))->post('/users', $payload)->assertRedirect();
        $this->assertDatabaseHas('users', ['username' => 'new-admin', 'role' => 'admin']);
    }
}
