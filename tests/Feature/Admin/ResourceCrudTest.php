<?php

namespace Tests\Feature\Admin;

use App\Models\Industry;
use App\Models\Machine;
use App\Models\Project;
use App\Models\Redirect;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResourceCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public_uploads');
        $this->actingAs(User::factory()->create(['role' => User::ROLE_EDITOR]));
    }

    public function test_create_generates_slug_converts_image_and_sanitises_rich_text(): void
    {
        $this->post('/admin/industries', [
            'name' => 'Battery Labels',
            'card_image' => UploadedFile::fake()->image('card.jpg', 1200, 800),
            'challenges' => '<div>Acid <strong>and</strong> heat<script>alert(1)</script></div>',
            'is_published' => '1',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $industry = Industry::sole();
        $this->assertSame('battery-labels', $industry->slug);
        $this->assertStringEndsWith('.webp', $industry->card_image);
        Storage::disk('public_uploads')->assertExists($industry->card_image);
        Storage::disk('public_uploads')->assertExists(str_replace('.webp', '-960.webp', $industry->card_image));
        $this->assertStringNotContainsString('<script>', $industry->challenges);
        $this->assertStringContainsString('<strong>and</strong>', $industry->challenges);
    }

    public function test_duplicate_names_get_unique_slugs_and_slug_must_be_unique(): void
    {
        Industry::create(['name' => 'Food']);
        Industry::create(['name' => 'Food']);
        $this->assertSame(['food', 'food-2'], Industry::orderBy('id')->pluck('slug')->all());

        $this->post('/admin/industries', ['name' => 'Other', 'slug' => 'food'])->assertSessionHasErrors('slug');
    }

    public function test_update_toggles_off_and_removing_image_deletes_files(): void
    {
        $this->post('/admin/industries', ['name' => 'Toys', 'is_published' => '1', 'card_image' => UploadedFile::fake()->image('a.png', 600, 400)]);
        $industry = Industry::sole();
        $path = $industry->card_image;

        $this->put("/admin/industries/{$industry->id}", ['name' => 'Toys', 'is_published' => '0', 'card_image_remove' => '1'])
            ->assertSessionHasNoErrors();

        $industry->refresh();
        $this->assertFalse($industry->is_published);
        $this->assertNull($industry->card_image);
        Storage::disk('public_uploads')->assertMissing($path);
    }

    public function test_project_syncs_relations_and_gallery(): void
    {
        $industry = Industry::create(['name' => 'Pharma']);

        $this->post('/admin/projects', [
            'title' => 'Pharma cartons',
            'industries' => [$industry->id],
            'gallery_new' => [UploadedFile::fake()->image('1.jpg'), UploadedFile::fake()->image('2.jpg')],
        ])->assertSessionHasNoErrors();

        $project = Project::sole();
        $this->assertSame([$industry->id], $project->industries->modelKeys());
        $this->assertCount(2, $project->gallery);

        $this->put("/admin/projects/{$project->id}", [
            'title' => 'Pharma cartons', 'industries' => '', 'gallery_remove' => [$project->gallery[0]],
        ])->assertSessionHasNoErrors();

        $project->refresh();
        $this->assertCount(0, $project->industries);
        $this->assertCount(1, $project->gallery);
    }

    public function test_machine_specs_drop_empty_rows(): void
    {
        $this->post('/admin/machines', [
            'name' => 'SM-74', 'category' => 'offset',
            'specs' => [['label' => 'Max sheet', 'value' => '520 × 740 mm'], ['label' => '', 'value' => 'ignored']],
        ])->assertSessionHasNoErrors();

        $this->assertSame([['label' => 'Max sheet', 'value' => '520 × 740 mm']], Machine::sole()->specs);
    }

    public function test_invalid_select_value_is_rejected(): void
    {
        $this->post('/admin/machines', ['name' => 'X', 'category' => 'laser'])->assertSessionHasErrors('category');
    }

    public function test_delete_removes_record(): void
    {
        $industry = Industry::create(['name' => 'Surgical']);

        $this->delete("/admin/industries/{$industry->id}")->assertRedirect('/admin/industries');
        $this->assertModelMissing($industry);
    }

    public function test_redirect_paths_are_normalised(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));

        $this->post('/admin/redirects', ['from_path' => 'https://powerstik.net/Products/Labels.html/?x=1', 'to_url' => '/capabilities/label-printing', 'status_code' => '301'])
            ->assertSessionHasNoErrors();

        $this->assertSame('/products/labels.html', Redirect::sole()->from_path);
    }
}
