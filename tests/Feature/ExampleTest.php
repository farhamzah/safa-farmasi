<?php

namespace Tests\Feature;

use App\Filament\Pages\ChangePassword;
use App\Filament\Resources\PortalApplications\Pages\EditPortalApplication;
use App\Models\Announcement;
use App\Models\AppCategory;
use App\Models\ApplicationClick;
use App\Models\LandingSetting;
use App\Models\PortalApplication;
use App\Models\User;
use App\Services\DuplicatePortalApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_returns_a_successful_response_and_shows_brand(): void
    {
        $response = $this->get('/');

        $response
            ->assertStatus(200)
            ->assertSee('SAFA UBP');
    }

    public function test_landing_page_renders_basic_meta_tags(): void
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('<title>SAFA UBP - Satu Akses Farmasi UBP</title>', false)
            ->assertSee('<meta name="description"', false)
            ->assertSee('<meta property="og:title"', false)
            ->assertSee('<meta property="og:description"', false)
            ->assertSee('<meta property="og:type" content="website">', false)
            ->assertSee('<meta name="theme-color" content="#0f766e">', false);
    }

    public function test_landing_page_uses_public_storage_hero_image_setting(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('safa/landing/hero.webp', 'hero');

        LandingSetting::query()->updateOrCreate([
            'key' => 'hero_image_url',
        ], [
            'group' => 'hero',
            'type' => 'image',
            'value' => 'safa/landing/hero.webp',
        ]);

        $this->get('/')
            ->assertStatus(200)
            ->assertSee('src="/storage/safa/landing/hero.webp"', false)
            ->assertSee('alt="Farmasi UBP"', false);
    }

    public function test_landing_hero_default_visual_is_not_a_service_card(): void
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertDontSee('Portal Layanan')
            ->assertDontSee('Akses cepat fakultas')
            ->assertDontSee('Upload gambar kefarmasian');
    }

    public function test_active_application_appears_on_landing_page(): void
    {
        $category = AppCategory::query()->create([
            'name' => 'Layanan Akademik',
            'slug' => 'layanan-akademik',
        ]);

        $application = PortalApplication::query()->create([
            'name' => 'Kerja Praktek',
            'slug' => 'kerja-praktek',
            'url' => 'https://safa.cloud/kerja-praktek',
            'status' => 'active',
            'button_label' => 'Buka KP',
        ]);

        $application->categories()->attach($category);

        $this->get('/')
            ->assertStatus(200)
            ->assertSee('Kerja Praktek')
            ->assertSee(route('applications.go', $application))
            ->assertSee('Buka KP');
    }

    public function test_application_thumbnail_uses_public_storage_url_on_landing_page(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('safa/applications/thumbnails/kp.png', 'thumbnail');

        $category = AppCategory::query()->create([
            'name' => 'Layanan Akademik',
            'slug' => 'layanan-akademik',
        ]);

        $application = PortalApplication::query()->create([
            'name' => 'Kerja Praktek',
            'slug' => 'kerja-praktek-thumb',
            'short_name' => 'KP',
            'thumbnail_path' => 'safa/applications/thumbnails/kp.png',
            'url' => 'https://safa.cloud/kerja-praktek',
            'status' => 'active',
        ]);

        $application->categories()->attach($category);

        $thumbnailUrl = $application->refresh()->thumbnail_url;

        $this->assertNotNull($thumbnailUrl);
        $this->assertStringContainsString('/storage/safa/applications/thumbnails/kp.png', $thumbnailUrl);

        $this->get('/')
            ->assertStatus(200)
            ->assertSee('src="'.$thumbnailUrl.'"', false)
            ->assertSee('alt="Kerja Praktek"', false);
    }

    public function test_application_thumbnail_falls_back_when_file_is_missing_or_path_is_temporary(): void
    {
        Storage::fake('public');

        $missingFileApplication = PortalApplication::query()->create([
            'name' => 'Kerja Praktek',
            'slug' => 'kerja-praktek-missing-thumb',
            'short_name' => 'KP',
            'thumbnail_path' => 'safa/applications/thumbnails/missing.png',
            'url' => 'https://safa.cloud/kerja-praktek',
            'status' => 'active',
        ]);

        $temporaryPathApplication = PortalApplication::query()->create([
            'name' => 'Upload Sementara',
            'slug' => 'upload-sementara',
            'short_name' => 'US',
            'thumbnail_path' => 'livewire-tmp/temp-file.png',
            'url' => 'https://safa.cloud/temp',
            'status' => 'active',
        ]);

        $this->assertNull($missingFileApplication->thumbnail_url);
        $this->assertNull($temporaryPathApplication->refresh()->thumbnail_path);
        $this->assertNull($temporaryPathApplication->thumbnail_url);
    }

    public function test_application_thumbnail_path_is_normalized_before_save(): void
    {
        $application = PortalApplication::query()->create([
            'name' => 'Kerja Praktek',
            'slug' => 'kerja-praktek-normalized-thumb',
            'short_name' => 'KP',
            'thumbnail_path' => storage_path('app/public/safa/applications/thumbnails/kp.png'),
            'url' => 'https://safa.cloud/kerja-praktek',
            'status' => 'active',
        ]);

        $this->assertSame(
            'safa/applications/thumbnails/kp.png',
            $application->refresh()->thumbnail_path,
        );
    }

    public function test_application_thumbnail_path_can_be_replaced_or_removed(): void
    {
        $application = PortalApplication::query()->create([
            'name' => 'Kerja Praktek',
            'slug' => 'kerja-praktek-replace-thumb',
            'short_name' => 'KP',
            'thumbnail_path' => 'safa/applications/thumbnails/old.png',
            'url' => 'https://safa.cloud/kerja-praktek',
            'status' => 'active',
        ]);

        $application->update([
            'thumbnail_path' => [
                'old-file-key' => 'safa/applications/thumbnails/old.png',
                'new-file-key' => 'safa/applications/thumbnails/kp.png',
            ],
        ]);

        $this->assertSame(
            'safa/applications/thumbnails/kp.png',
            $application->refresh()->thumbnail_path,
        );

        $application->update([
            'thumbnail_path' => [],
        ]);

        $this->assertNull($application->refresh()->thumbnail_path);
    }

    public function test_edit_application_form_replaces_thumbnail_path(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('safa/applications/thumbnails/old.png', 'old-thumbnail');

        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $application = PortalApplication::query()->create([
            'name' => 'Kerja Praktek',
            'slug' => 'kerja-praktek-filament-thumb',
            'short_name' => 'KP',
            'thumbnail_path' => 'safa/applications/thumbnails/old.png',
            'url' => 'https://safa.cloud/kerja-praktek',
            'status' => 'active',
            'short_description' => 'Pengajuan dan monitoring administrasi kerja praktek mahasiswa.',
        ]);

        Livewire::actingAs($admin)
            ->test(EditPortalApplication::class, ['record' => $application->getKey()])
            ->set('data.thumbnail_path.new-file-key', UploadedFile::fake()->image('KP.png')->size(1500))
            ->call('save')
            ->assertHasNoErrors();

        $application->refresh();

        $this->assertNotSame('safa/applications/thumbnails/old.png', $application->thumbnail_path);
        $this->assertStringStartsWith('safa/applications/thumbnails/', $application->thumbnail_path);
        $this->assertStringEndsWith('.png', $application->thumbnail_path);
        Storage::disk('public')->assertExists($application->thumbnail_path);
    }

    public function test_edit_application_form_can_remove_thumbnail_path(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('safa/applications/thumbnails/old.png', 'old-thumbnail');

        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $application = PortalApplication::query()->create([
            'name' => 'Kerja Praktek',
            'slug' => 'kerja-praktek-remove-thumb',
            'short_name' => 'KP',
            'thumbnail_path' => 'safa/applications/thumbnails/old.png',
            'url' => 'https://safa.cloud/kerja-praktek',
            'status' => 'active',
            'short_description' => 'Pengajuan dan monitoring administrasi kerja praktek mahasiswa.',
        ]);

        Livewire::actingAs($admin)
            ->test(EditPortalApplication::class, ['record' => $application->getKey()])
            ->set('data.thumbnail_path', [])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertNull($application->refresh()->thumbnail_path);
    }

    public function test_inactive_application_does_not_appear_on_landing_page(): void
    {
        $category = AppCategory::query()->create([
            'name' => 'Arsip',
            'slug' => 'arsip',
        ]);

        $application = PortalApplication::query()->create([
            'name' => 'Aplikasi Nonaktif',
            'slug' => 'aplikasi-nonaktif',
            'url' => 'https://safa.cloud/nonaktif',
            'status' => 'inactive',
        ]);

        $application->categories()->attach($category);

        $this->get('/')
            ->assertStatus(200)
            ->assertDontSee('Aplikasi Nonaktif')
            ->assertDontSee('https://safa.cloud/nonaktif');
    }

    public function test_application_with_inactive_toggle_does_not_appear_on_landing_page(): void
    {
        $category = AppCategory::query()->create([
            'name' => 'Internal',
            'slug' => 'internal',
        ]);

        $application = PortalApplication::query()->create([
            'name' => 'Aplikasi Disembunyikan',
            'slug' => 'aplikasi-disembunyikan',
            'url' => 'https://safa.cloud/hidden',
            'status' => 'active',
            'is_active' => false,
        ]);

        $application->categories()->attach($category);

        $this->get('/')
            ->assertStatus(200)
            ->assertDontSee('Aplikasi Disembunyikan')
            ->assertDontSee('https://safa.cloud/hidden');
    }

    public function test_search_and_filter_do_not_show_inactive_application(): void
    {
        $category = AppCategory::query()->create([
            'name' => 'Formulir',
            'slug' => 'formulir',
        ]);

        $active = PortalApplication::query()->create([
            'name' => 'Download Formulir',
            'slug' => 'download-formulir',
            'short_description' => 'Unduh formulir akademik.',
            'url' => 'https://safa.cloud/formulir',
            'status' => 'active',
        ]);

        $inactive = PortalApplication::query()->create([
            'name' => 'Formulir Lama',
            'slug' => 'formulir-lama',
            'short_description' => 'Arsip formulir.',
            'url' => 'https://safa.cloud/formulir-lama',
            'status' => 'inactive',
        ]);

        $active->categories()->attach($category);
        $inactive->categories()->attach($category);

        $this->get('/?q=formulir&category=formulir')
            ->assertStatus(200)
            ->assertSee('Download Formulir')
            ->assertDontSee('Formulir Lama')
            ->assertDontSee('https://safa.cloud/formulir-lama');
    }

    public function test_maintenance_and_coming_soon_applications_are_not_active_links(): void
    {
        $category = AppCategory::query()->create([
            'name' => 'Layanan',
            'slug' => 'layanan',
        ]);

        $maintenance = PortalApplication::query()->create([
            'name' => 'Sistem Maintenance',
            'slug' => 'sistem-maintenance',
            'url' => 'https://safa.cloud/maintenance',
            'status' => 'maintenance',
        ]);

        $comingSoon = PortalApplication::query()->create([
            'name' => 'Sistem Baru',
            'slug' => 'sistem-baru',
            'url' => 'https://safa.cloud/baru',
            'status' => 'coming_soon',
        ]);

        $maintenance->categories()->attach($category);
        $comingSoon->categories()->attach($category);

        $this->get('/')
            ->assertStatus(200)
            ->assertSee('Sistem Maintenance')
            ->assertSee('Maintenance')
            ->assertSee('Sistem Baru')
            ->assertSee('Segera Hadir')
            ->assertDontSee('https://safa.cloud/maintenance')
            ->assertDontSee('https://safa.cloud/baru');
    }

    public function test_internal_application_has_active_external_link_attributes(): void
    {
        $category = AppCategory::query()->create([
            'name' => 'Internal',
            'slug' => 'layanan-internal',
        ]);

        $application = PortalApplication::query()->create([
            'name' => 'Portal Internal',
            'slug' => 'portal-internal',
            'url' => 'https://safa.cloud/internal',
            'status' => 'internal',
            'button_label' => 'Masuk Internal',
        ]);

        $application->categories()->attach($category);

        $this->get('/')
            ->assertStatus(200)
            ->assertSee('Portal Internal')
            ->assertSee(route('applications.go', $application))
            ->assertSee('target="_blank"', false)
            ->assertSee('rel="noopener noreferrer"', false)
            ->assertSee('Masuk Internal');
    }

    public function test_go_route_records_click_and_redirects_to_target_url(): void
    {
        $application = PortalApplication::query()->create([
            'name' => 'Helpdesk',
            'slug' => 'helpdesk',
            'url' => 'https://safa.cloud/helpdesk',
            'status' => 'active',
        ]);

        $this->get(route('applications.go', $application))
            ->assertRedirect('https://safa.cloud/helpdesk');

        $this->assertDatabaseHas('application_clicks', [
            'portal_application_id' => $application->id,
            'application_name' => 'Helpdesk',
            'target_url' => 'https://safa.cloud/helpdesk',
        ]);

        $this->assertNotNull(ApplicationClick::query()->first()?->ip_hash);
    }

    public function test_inactive_application_cannot_be_accessed_through_go_route(): void
    {
        $application = PortalApplication::query()->create([
            'name' => 'Aplikasi Nonaktif',
            'slug' => 'go-nonaktif',
            'url' => 'https://safa.cloud/nonaktif',
            'status' => 'inactive',
        ]);

        $this->get(route('applications.go', $application))
            ->assertNotFound();

        $hiddenApplication = PortalApplication::query()->create([
            'name' => 'Aplikasi Hidden',
            'slug' => 'go-hidden',
            'url' => 'https://safa.cloud/hidden',
            'status' => 'active',
            'is_active' => false,
        ]);

        $this->get(route('applications.go', $hiddenApplication))
            ->assertNotFound();

        $this->assertDatabaseCount('application_clicks', 0);
    }

    public function test_active_application_without_url_is_not_rendered_as_active_link(): void
    {
        $category = AppCategory::query()->create([
            'name' => 'Internal',
            'slug' => 'internal',
        ]);

        $application = PortalApplication::query()->create([
            'name' => 'Aplikasi Tanpa URL',
            'slug' => 'aplikasi-tanpa-url',
            'status' => 'active',
        ]);

        $application->categories()->attach($category);

        $this->get('/')
            ->assertStatus(200)
            ->assertSee('Aplikasi Tanpa URL')
            ->assertDontSee('href=""', false);
    }

    public function test_inactive_category_does_not_appear_in_landing_filter(): void
    {
        AppCategory::query()->create([
            'name' => 'Kategori Nonaktif',
            'slug' => 'kategori-nonaktif',
            'is_active' => false,
        ]);

        $this->get('/')
            ->assertStatus(200)
            ->assertDontSee('Kategori Nonaktif');
    }

    public function test_empty_state_appears_when_search_has_no_results(): void
    {
        $this->get('/?q=tidak-ada-aplikasi')
            ->assertStatus(200)
            ->assertSee('Aplikasi tidak ditemukan.')
            ->assertSee('Coba gunakan kata kunci lain atau pilih kategori Semua.');
    }

    public function test_contact_section_has_safe_fallback_when_contact_settings_are_empty(): void
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('Hubungi Tata Usaha Fakultas Farmasi UBP untuk bantuan akses layanan.');
    }

    public function test_active_announcement_appears_on_landing_page(): void
    {
        Announcement::query()->create([
            'title' => 'Pendaftaran dibuka',
            'body' => 'Layanan sudah tersedia.',
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
        ]);

        $this->get('/')
            ->assertStatus(200)
            ->assertSee('href="#pengumuman"', false)
            ->assertSee('id="pengumuman"', false)
            ->assertSee('Pengumuman')
            ->assertSee('Pendaftaran dibuka');
    }

    public function test_landing_page_does_not_expose_admin_link(): void
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertDontSee('href="/admin"', false)
            ->assertDontSee('Masuk ke panel admin')
            ->assertDontSee('>Admin<', false);
    }

    public function test_expired_announcement_does_not_appear_on_landing_page(): void
    {
        Announcement::query()->create([
            'title' => 'Pengumuman Lama',
            'is_active' => true,
            'starts_at' => now()->subDays(3),
            'ends_at' => now()->subDay(),
        ]);

        $this->get('/')
            ->assertStatus(200)
            ->assertDontSee('Pengumuman Lama');
    }

    public function test_non_admin_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admin_can_access_admin_panel_after_login(): void
    {
        $user = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertStatus(200);
    }

    public function test_admin_can_access_application_resource_after_login(): void
    {
        $user = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($user)
            ->get('/admin/portal-applications')
            ->assertStatus(200);
    }

    public function test_admin_can_access_polished_resource_tables_after_login(): void
    {
        $user = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($user)->get('/admin/app-categories')->assertStatus(200);
        $this->actingAs($user)->get('/admin/announcements')->assertStatus(200);
        $this->actingAs($user)->get('/admin/landing-settings')->assertStatus(200);
    }

    public function test_admin_can_access_create_forms_after_login(): void
    {
        $user = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($user)->get('/admin/portal-applications/create')->assertStatus(200);
        $this->actingAs($user)->get('/admin/app-categories/create')->assertStatus(200);
        $this->actingAs($user)->get('/admin/announcements/create')->assertStatus(200);
        $this->actingAs($user)->get('/admin/landing-settings/create')->assertStatus(200);
    }

    public function test_admin_can_access_change_password_page_and_update_password(): void
    {
        $user = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($user)
            ->get('/admin/ubah-password')
            ->assertStatus(200)
            ->assertSee('Ubah Password');

        Livewire::actingAs($user)
            ->test(ChangePassword::class)
            ->set('data.current_password', 'password')
            ->set('data.password', 'password-baru')
            ->set('data.password_confirmation', 'password-baru')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertTrue(Hash::check('password-baru', $user->refresh()->password));
    }

    public function test_change_password_page_requires_admin_login(): void
    {
        $nonAdmin = User::factory()->create([
            'is_admin' => false,
        ]);

        $this->get('/admin/ubah-password')->assertRedirect('/admin/login');

        $this->actingAs($nonAdmin)
            ->get('/admin/ubah-password')
            ->assertForbidden();
    }

    public function test_duplicate_application_creates_unique_slug_and_copies_categories(): void
    {
        $category = AppCategory::query()->create([
            'name' => 'Layanan',
            'slug' => 'layanan',
        ]);

        $application = PortalApplication::query()->create([
            'name' => 'Helpdesk',
            'slug' => 'helpdesk',
            'short_description' => 'Bantuan akses.',
            'long_description' => 'Bantuan akses layanan digital.',
            'url' => 'https://safa.cloud/helpdesk',
            'status' => 'active',
            'button_label' => 'Masuk',
            'accent_color' => '#0f766e',
            'sort_order' => 4,
            'is_featured' => true,
            'is_active' => true,
            'open_in_new_tab' => true,
        ]);

        $application->categories()->attach($category);

        $copy = app(DuplicatePortalApplication::class)->duplicate($application);

        $this->assertNotSame($application->slug, $copy->slug);
        $this->assertSame('Helpdesk Salinan', $copy->name);
        $this->assertSame('Bantuan akses.', $copy->short_description);
        $this->assertSame('#0f766e', $copy->accent_color);
        $this->assertTrue($copy->categories()->whereKey($category->id)->exists());
    }

    public function test_application_export_is_admin_only(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $nonAdmin = User::factory()->create(['is_admin' => false]);

        $this->get('/admin/exports/applications')->assertRedirect('/admin/login');

        $this->actingAs($nonAdmin)
            ->get('/admin/exports/applications')
            ->assertForbidden();

        $response = $this->actingAs($admin)->get('/admin/exports/applications');
        $content = $response->streamedContent();

        $response
            ->assertOk()
            ->assertHeader('content-disposition');

        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString(
            'name,slug,short_description,url,status,button_label,categories,sort_order,is_featured,is_active,open_in_new_tab,updated_at',
            $content,
        );
    }

    public function test_category_export_is_admin_only(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $nonAdmin = User::factory()->create(['is_admin' => false]);

        $this->get('/admin/exports/categories')->assertRedirect('/admin/login');

        $this->actingAs($nonAdmin)
            ->get('/admin/exports/categories')
            ->assertForbidden();

        $response = $this->actingAs($admin)->get('/admin/exports/categories');
        $content = $response->streamedContent();

        $response
            ->assertOk()
            ->assertHeader('content-disposition');

        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString(
            'name,slug,description,icon,sort_order,is_active,applications_count,updated_at',
            $content,
        );
    }

    public function test_click_export_is_admin_only(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $nonAdmin = User::factory()->create(['is_admin' => false]);

        ApplicationClick::query()->create([
            'application_name' => 'Helpdesk',
            'target_url' => 'https://safa.cloud/helpdesk',
            'clicked_at' => now(),
            'user_agent' => 'Test Browser',
        ]);

        $this->get('/admin/exports/clicks')->assertRedirect('/admin/login');

        $this->actingAs($nonAdmin)
            ->get('/admin/exports/clicks')
            ->assertForbidden();

        $response = $this->actingAs($admin)->get('/admin/exports/clicks');
        $content = $response->streamedContent();

        $response
            ->assertOk()
            ->assertHeader('content-disposition');

        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('application_name,target_url,clicked_at,user_agent', $content);
        $this->assertStringContainsString('Helpdesk', $content);
        $this->assertStringNotContainsString('ip_hash', $content);
    }
}
