<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    private Admin $actor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actor = Admin::factory()->create();
    }

    public function test_admin_list_is_accessible(): void
    {
        $this->actingAs($this->actor, 'admin')
            ->get(route('admin.admins.index'))
            ->assertOk();
    }

    public function test_admin_list_is_paginated(): void
    {
        Admin::factory()->count(35)->create();

        $this->actingAs($this->actor, 'admin')
            ->get(route('admin.admins.index'))
            ->assertOk();
    }

    public function test_admin_list_search_by_keyword(): void
    {
        Admin::factory()->create(['name' => 'John Doe']);
        Admin::factory()->create(['name' => 'Jane Smith']);

        $this->actingAs($this->actor, 'admin')
            ->get(route('admin.admins.index', ['keyword' => 'John']))
            ->assertOk()
            ->assertSee('John Doe')
            ->assertDontSee('Jane Smith');
    }

    public function test_can_create_admin(): void
    {
        $this->actingAs($this->actor, 'admin')
            ->post(route('admin.admins.store'), [
                'name'     => 'New Admin',
                'email'    => 'newadmin@example.com',
                'password' => 'password123',
            ])
            ->assertRedirect(route('admin.admins.index'));

        $this->assertDatabaseHas('admins', ['email' => 'newadmin@example.com']);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->actor, 'admin')
            ->post(route('admin.admins.store'), [])
            ->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_store_validates_unique_email(): void
    {
        Admin::factory()->create(['email' => 'taken@example.com']);

        $this->actingAs($this->actor, 'admin')
            ->post(route('admin.admins.store'), [
                'name'     => 'Another Admin',
                'email'    => 'taken@example.com',
                'password' => 'password123',
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_can_update_admin(): void
    {
        $target = Admin::factory()->create();

        $this->actingAs($this->actor, 'admin')
            ->put(route('admin.admins.update', $target), [
                'name'  => 'Updated Name',
                'email' => $target->email,
            ])
            ->assertRedirect(route('admin.admins.index'));

        $this->assertDatabaseHas('admins', ['id' => $target->id, 'name' => 'Updated Name']);
    }

    public function test_can_delete_another_admin(): void
    {
        $target = Admin::factory()->create();

        $this->actingAs($this->actor, 'admin')
            ->delete(route('admin.admins.destroy', $target))
            ->assertRedirect(route('admin.admins.index'));

        $this->assertSoftDeleted('admins', ['id' => $target->id]);
    }

    public function test_cannot_delete_self(): void
    {
        $this->actingAs($this->actor, 'admin')
            ->delete(route('admin.admins.destroy', $this->actor))
            ->assertRedirect(route('admin.admins.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('admins', ['id' => $this->actor->id, 'deleted_at' => null]);
    }

    public function test_cannot_delete_last_admin(): void
    {
        /** @var Admin $only */
        $only = Admin::factory()->create();
        Admin::withoutEvents(static function () use ($only): void {
            Admin::where('id', '!=', (int) $only->id)->forceDelete();
        });

        $this->actingAs($only, 'admin')
            ->delete(route('admin.admins.destroy', $only))
            ->assertRedirect(route('admin.admins.index'))
            ->assertSessionHas('error');
    }

    public function test_can_toggle_admin_status(): void
    {
        $target = Admin::factory()->create(['is_active' => true]);

        $this->actingAs($this->actor, 'admin')
            ->post(route('admin.admins.toggle-status', $target))
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('admins', ['id' => $target->id, 'is_active' => false]);
    }

    public function test_guest_cannot_access_admin_management(): void
    {
        $target = Admin::factory()->create();

        $this->get(route('admin.admins.index'))->assertRedirect(route('admin.login'));
        $this->post(route('admin.admins.store'), [])->assertRedirect(route('admin.login'));
        $this->delete(route('admin.admins.destroy', $target))->assertRedirect(route('admin.login'));
    }

    public function test_password_is_hashed_on_create(): void
    {
        $this->actingAs($this->actor, 'admin')
            ->post(route('admin.admins.store'), [
                'name'     => 'Hashed Admin',
                'email'    => 'hash@example.com',
                'password' => 'plaintext',
            ]);

        $admin = Admin::where('email', 'hash@example.com')->first();
        $this->assertTrue(Hash::check('plaintext', $admin->password));
        $this->assertNotEquals('plaintext', $admin->password);
    }
}
