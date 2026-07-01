<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\CoreBridgeAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SafaCoreBridgeAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.connections.core_test' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => false,
            ],
            'safa_auth.core.connection' => 'core_test',
            'safa_auth.core.app_code' => 'safa-ubp',
            'safa_auth.core.allowed_roles' => ['admin-safa'],
        ]);

        DB::purge('core_test');
        Schema::connection('core_test')->create('users', function ($table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('username')->nullable();
            $table->string('identity_number')->nullable();
            $table->string('password');
            $table->boolean('active')->default(true);
            $table->boolean('must_change_password')->default(false);
        });

        Schema::connection('core_test')->create('user_app_accesses', function ($table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('app_code');
            $table->string('role_slug')->nullable();
            $table->boolean('is_active')->default(true);
        });
    }

    public function test_safa_auth_mode_defaults_to_local(): void
    {
        $this->assertSame('local', config('safa_auth.mode'));
    }

    public function test_core_bridge_creates_local_admin_user_when_core_access_is_valid(): void
    {
        config(['safa_auth.mode' => 'core_bridge']);

        $coreUserId = DB::connection('core_test')->table('users')->insertGetId([
            'name' => 'Admin SAFA Core',
            'email' => 'admin.safa@example.test',
            'username' => 'admin-safa',
            'identity_number' => 'SAFA001',
            'password' => Hash::make('secret-core'),
            'active' => true,
            'must_change_password' => false,
        ]);

        DB::connection('core_test')->table('user_app_accesses')->insert([
            'user_id' => $coreUserId,
            'app_code' => 'safa-ubp',
            'role_slug' => 'admin-safa',
            'is_active' => true,
        ]);

        $result = app(CoreBridgeAuthService::class)->attempt('admin.safa@example.test', 'secret-core');

        $this->assertTrue($result['ok']);
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'admin.safa@example.test',
            'name' => 'Admin SAFA Core',
            'is_admin' => true,
        ]);
        $this->assertFalse(Hash::check('secret-core', User::where('email', 'admin.safa@example.test')->first()->password));
    }

    public function test_core_bridge_denies_user_without_safa_admin_access(): void
    {
        config(['safa_auth.mode' => 'core_bridge']);

        DB::connection('core_test')->table('users')->insert([
            'name' => 'No Access',
            'email' => 'no.access@example.test',
            'password' => Hash::make('secret-core'),
            'active' => true,
            'must_change_password' => false,
        ]);

        $result = app(CoreBridgeAuthService::class)->attempt('no.access@example.test', 'secret-core');

        $this->assertFalse($result['ok']);
        $this->assertSame('core_app_access_denied', $result['reason']);
        $this->assertGuest();
        $this->assertDatabaseMissing('users', [
            'email' => 'no.access@example.test',
        ]);
    }
}
