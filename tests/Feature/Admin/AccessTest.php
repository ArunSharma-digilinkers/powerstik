<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_sent_to_login(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
        $this->get('/admin/industries')->assertRedirect(route('admin.login'));
    }

    public function test_login_works_and_rejects_wrong_password(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_EDITOR]);

        $this->post('/admin/login', ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post('/admin/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_inactive_users_cannot_log_in(): void
    {
        $user = User::factory()->create(['is_active' => false]);

        $this->post('/admin/login', ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_roles_limit_what_each_user_can_open(): void
    {
        $sales = User::factory()->create(['role' => User::ROLE_SALES]);
        $editor = User::factory()->create(['role' => User::ROLE_EDITOR]);

        $this->actingAs($sales)->get('/admin/leads')->assertOk();
        $this->actingAs($sales)->get('/admin/industries')->assertForbidden();
        $this->actingAs($editor)->get('/admin/industries')->assertOk();
        $this->actingAs($editor)->get('/admin/leads')->assertForbidden();
        $this->actingAs($editor)->get('/admin/users')->assertForbidden();
        $this->actingAs($editor)->get('/admin/settings')->assertForbidden();
    }

    public function test_every_admin_screen_renders_for_an_admin(): void
    {
        $this->seed(ContentSeeder::class);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin);
        foreach (['/admin', '/admin/leads', '/admin/settings', '/admin/system'] as $url) {
            $this->get($url)->assertOk();
        }
        foreach (array_keys(config('admin.resources')) as $slug) {
            $this->get("/admin/{$slug}")->assertOk();
            $this->get("/admin/{$slug}/create")->assertOk();
        }
        $this->get('/admin/industries/1/edit')->assertOk()->assertSee('Battery');
        $this->get('/admin/machines/1/edit')->assertOk();
    }

    public function test_ops_endpoint_requires_the_token(): void
    {
        config(['powerstik.ops_token' => 'secret-token']);

        $this->post('/_ops/deploy')->assertNotFound();
        $this->withToken('wrong')->post('/_ops/deploy')->assertNotFound();

        config(['powerstik.ops_token' => '']);
        $this->withToken('')->post('/_ops/deploy')->assertNotFound();
    }
}
