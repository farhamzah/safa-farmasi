<?php

namespace Tests\Feature;

use App\Models\AppCategory;
use App\Models\PortalApplication;
use Database\Seeders\LabPortalCardSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LabPortalCardPreparationTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_registers_lab_portal_card_idempotently(): void
    {
        $this->seed(LabPortalCardSeeder::class);
        $this->seed(LabPortalCardSeeder::class);

        $this->assertSame(1, PortalApplication::where('slug', 'lab-farmasi-ubp')->count());
        $this->assertDatabaseHas('app_categories', [
            'slug' => 'laboratorium',
            'name' => 'Laboratorium',
        ]);
        $this->assertDatabaseHas('portal_applications', [
            'slug' => 'lab-farmasi-ubp',
            'name' => 'Lab Farmasi UBP',
            'short_name' => 'LAB',
            'url' => 'http://127.0.0.1:8006/dashboard',
            'status' => 'internal',
            'button_label' => 'Buka Lab',
            'is_active' => true,
        ]);

        $application = PortalApplication::where('slug', 'lab-farmasi-ubp')->firstOrFail();
        $this->assertTrue($application->categories()->where('slug', 'laboratorium')->exists());
        $this->assertStringNotContainsString('token=', $application->url);
        $this->assertStringNotContainsString('password', $application->url);
        $this->assertStringNotContainsString('secret', $application->url);
    }

    public function test_lab_card_appears_on_landing_without_token_url(): void
    {
        $category = AppCategory::query()->create([
            'name' => 'Laboratorium',
            'slug' => 'laboratorium',
        ]);
        $application = PortalApplication::query()->create([
            'app_category_id' => $category->id,
            'name' => 'Lab Farmasi UBP',
            'slug' => 'lab-farmasi-ubp',
            'short_name' => 'LAB',
            'description' => 'Sistem Operasional Laboratorium',
            'short_description' => 'Sistem Operasional Laboratorium',
            'url' => 'http://127.0.0.1:8006/dashboard',
            'status' => 'internal',
            'button_label' => 'Buka Lab',
            'is_active' => true,
        ]);
        $application->categories()->attach($category);

        $this->get('/')
            ->assertStatus(200)
            ->assertSee('Lab Farmasi UBP')
            ->assertSee(route('applications.go', $application))
            ->assertDontSee('token=', false)
            ->assertDontSee('password', false)
            ->assertDontSee('secret', false);
    }
}
