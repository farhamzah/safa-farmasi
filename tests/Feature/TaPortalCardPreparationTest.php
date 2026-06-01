<?php

namespace Tests\Feature;

use App\Models\AppCategory;
use App\Models\PortalApplication;
use Database\Seeders\TaPortalCardSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaPortalCardPreparationTest extends TestCase
{
    use RefreshDatabase;

    public function test_ta_portal_card_seeder_registers_card_idempotently(): void
    {
        $this->seed(TaPortalCardSeeder::class);
        $this->seed(TaPortalCardSeeder::class);

        $this->assertSame(1, PortalApplication::where('slug', 'ta-farmasi-ubp')->count());
        $this->assertDatabaseHas('app_categories', [
            'slug' => 'layanan-akademik',
            'name' => 'Layanan Akademik',
        ]);
        $this->assertDatabaseHas('portal_applications', [
            'slug' => 'ta-farmasi-ubp',
            'name' => 'TA Farmasi UBP',
            'short_name' => 'TA',
            'url' => 'http://127.0.0.1:8007',
            'status' => 'internal',
            'button_label' => 'Buka TA',
            'is_active' => true,
        ]);

        $application = PortalApplication::where('slug', 'ta-farmasi-ubp')->firstOrFail();
        $this->assertTrue($application->categories()->where('slug', 'layanan-akademik')->exists());
        $this->assertSafeNormalUrl($application->url);
    }

    public function test_ta_card_appears_on_landing_without_token_url(): void
    {
        $category = AppCategory::query()->create([
            'name' => 'Layanan Akademik',
            'slug' => 'layanan-akademik',
        ]);
        $application = PortalApplication::query()->create([
            'app_category_id' => $category->id,
            'name' => 'TA Farmasi UBP',
            'slug' => 'ta-farmasi-ubp',
            'short_name' => 'TA',
            'description' => 'Pengelolaan Tugas Akhir Farmasi UBP.',
            'short_description' => 'Pengelolaan Tugas Akhir',
            'url' => 'http://127.0.0.1:8007',
            'status' => 'internal',
            'button_label' => 'Buka TA',
            'is_active' => true,
        ]);
        $application->categories()->attach($category);

        $this->get('/')
            ->assertStatus(200)
            ->assertSee('TA Farmasi UBP')
            ->assertSee(route('applications.go', $application))
            ->assertDontSee('token=', false)
            ->assertDontSee('client_secret', false)
            ->assertDontSee('password', false)
            ->assertDontSee('secret', false);
    }

    private function assertSafeNormalUrl(string $url): void
    {
        $this->assertStringStartsWith('http://127.0.0.1:8007', $url);
        $this->assertStringNotContainsString('token=', $url);
        $this->assertStringNotContainsString('client_secret', $url);
        $this->assertStringNotContainsString('password', $url);
        $this->assertStringNotContainsString('secret', $url);
        $this->assertStringNotContainsString('autologin', $url);
    }
}
