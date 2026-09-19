<?php

namespace Tests\Feature\Admin;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadsAndUsersTest extends TestCase
{
    use RefreshDatabase;

    private function lead(array $attrs = []): Lead
    {
        return Lead::create($attrs + ['type' => 'quote', 'name' => 'Ravi', 'email' => 'ravi@example.com', 'payload' => ['quantity' => 5000, 'form' => 'roll']]);
    }

    public function test_sales_can_view_filter_update_and_export_leads(): void
    {
        $sales = User::factory()->create(['role' => User::ROLE_SALES]);
        $lead = $this->lead();
        $this->lead(['type' => 'callback', 'name' => 'Spammer', 'status' => 'spam']);

        $this->actingAs($sales)->get('/admin/leads')->assertOk()->assertSee('Ravi')->assertDontSee('Spammer');
        $this->get('/admin/leads?type=callback&status=spam')->assertSee('Spammer')->assertDontSee('Ravi');
        $this->get("/admin/leads/{$lead->id}")->assertOk()->assertSee('5000');

        $this->put("/admin/leads/{$lead->id}", ['status' => 'contacted', 'assigned_to' => $sales->id, 'notes' => 'Called'])->assertSessionHasNoErrors();
        $this->assertSame('contacted', $lead->fresh()->status);

        $csv = $this->get('/admin/leads/export')->assertOk()->streamedContent();
        $this->assertStringContainsString('Ravi', $csv);

        $this->delete("/admin/leads/{$lead->id}")->assertForbidden();
    }

    public function test_admin_manages_staff_and_password_is_kept_when_blank(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin);

        $this->post('/admin/users', ['name' => 'Neha', 'email' => 'neha@example.com', 'role' => 'sales', 'password' => 'short'])
            ->assertSessionHasErrors('password');
        $this->post('/admin/users', ['name' => 'Neha', 'email' => 'neha@example.com', 'role' => 'sales', 'password' => 'long-enough-pass', 'is_active' => '1'])
            ->assertSessionHasNoErrors();

        $neha = User::where('email', 'neha@example.com')->sole();
        $hash = $neha->password;

        $this->put("/admin/users/{$neha->id}", ['name' => 'Neha K', 'email' => 'neha@example.com', 'role' => 'sales', 'password' => '', 'is_active' => '1'])
            ->assertSessionHasNoErrors();
        $this->assertSame($hash, $neha->fresh()->password);

        $this->delete("/admin/users/{$admin->id}")->assertStatus(422);
    }
}
