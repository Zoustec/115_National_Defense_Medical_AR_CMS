<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible(): void
    {
        $this->get(route('admin.login'))->assertOk();
    }

    public function test_authenticated_admin_is_redirected_from_login(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get(route('admin.login'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_can_login_with_valid_credentials(): void
    {
        $admin = Admin::factory()->create([
            'password'  => Hash::make('secret123'),
            'is_active' => true,
        ]);

        $this->post(route('admin.login') . '/login', [
            'email'    => $admin->email,
            'password' => 'secret123',
        ])->assertRedirect(route('admin.dashboard'));
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $admin = Admin::factory()->create([
            'password'  => Hash::make('correct'),
            'is_active' => true,
        ]);

        $this->post(route('admin.login') . '/login', [
            'email'    => $admin->email,
            'password' => 'wrong',
        ])->assertRedirect()->assertSessionHasErrors('error');
    }

    public function test_inactive_admin_cannot_login(): void
    {
        $admin = Admin::factory()->create([
            'password'  => Hash::make('secret123'),
            'is_active' => false,
        ]);

        $this->post(route('admin.login') . '/login', [
            'email'    => $admin->email,
            'password' => 'secret123',
        ])->assertRedirect()->assertSessionHasErrors('error');
    }

    public function test_login_validates_required_fields(): void
    {
        $this->post(route('admin.login') . '/login', [])
            ->assertSessionHasErrors(['email', 'password']);
    }

    public function test_admin_can_logout(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.logout'))
            ->assertRedirect(route('admin.login'));

        $this->assertGuest('admin');
    }

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }
}
