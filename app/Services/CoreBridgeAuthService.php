<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class CoreBridgeAuthService
{
    private ?string $failureReason = null;

    public function attempt(string $login, string $password, bool $remember = false): array
    {
        $this->failureReason = null;
        $mode = config('safa_auth.mode', 'local');

        if ($mode === 'local') {
            return $this->attemptLocal($login, $password, $remember);
        }

        $coreResult = $this->attemptCore($login, $password, $remember);
        if ($coreResult['ok']) {
            return $coreResult;
        }

        if ($mode === 'core_bridge_with_local_fallback' && in_array($coreResult['reason'], ['core_unavailable', 'invalid_credentials'], true)) {
            Log::warning('SAFA admin auth local fallback attempted after Core bridge failure.', [
                'login' => $this->normalize($login),
                'reason' => $coreResult['reason'],
            ]);

            return $this->attemptLocal($login, $password, $remember, 'local_fallback');
        }

        return $coreResult;
    }

    public function explainFailureReason(): ?string
    {
        return $this->failureReason;
    }

    private function attemptCore(string $login, string $password, bool $remember): array
    {
        $coreUser = $this->findCoreUser($login);

        if (! $coreUser || ! Hash::check($password, (string) $coreUser->password)) {
            $this->failureReason ??= 'invalid_credentials';

            return $this->result(false, null, $this->failureReason, 'core_bridge');
        }

        if (! (bool) $coreUser->active) {
            $this->failureReason = 'core_user_inactive';

            return $this->result(false, null, $this->failureReason, 'core_bridge');
        }

        if ((bool) ($coreUser->must_change_password ?? false)) {
            $this->failureReason = 'core_password_must_change';

            return $this->result(false, null, $this->failureReason, 'core_bridge');
        }

        if (! $this->hasSafaAdminAccess((int) $coreUser->id)) {
            $this->failureReason = 'core_app_access_denied';

            return $this->result(false, null, $this->failureReason, 'core_bridge');
        }

        $localUser = $this->syncLocalAdminUser($coreUser);

        Auth::login($localUser, $remember);

        Log::info('SAFA admin auth Core bridge login success.', [
            'email' => $this->normalize((string) $coreUser->email),
            'core_user_id' => $coreUser->id,
            'local_user_id' => $localUser->id,
        ]);

        return $this->result(true, $localUser, null, 'core_bridge');
    }

    private function attemptLocal(string $login, string $password, bool $remember = false, string $via = 'local'): array
    {
        $ok = Auth::attempt(['email' => $login, 'password' => $password], $remember);

        if (! $ok || ! Auth::user()?->is_admin) {
            Auth::logout();
            $this->failureReason = 'invalid_credentials';

            return $this->result(false, null, $this->failureReason, $via);
        }

        return $this->result(true, Auth::user(), null, $via);
    }

    private function findCoreUser(string $login): ?object
    {
        $normalized = $this->normalize($login);

        try {
            return DB::connection((string) config('safa_auth.core.connection', 'core'))
                ->table('users')
                ->where(function ($query) use ($login, $normalized): void {
                    $query->whereRaw('LOWER(TRIM(email)) = ?', [$normalized])
                        ->orWhereRaw('LOWER(TRIM(username)) = ?', [$normalized])
                        ->orWhere('identity_number', trim($login));
                })
                ->first();
        } catch (Throwable $exception) {
            $this->failureReason = 'core_unavailable';
            Log::warning('SAFA admin auth Core lookup failed.', [
                'login' => $normalized,
                'error' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    private function hasSafaAdminAccess(int $coreUserId): bool
    {
        $appCode = (string) config('safa_auth.core.app_code', 'safa-ubp');
        $allowedRoles = config('safa_auth.core.allowed_roles', ['admin-safa']);

        return DB::connection((string) config('safa_auth.core.connection', 'core'))
            ->table('user_app_accesses')
            ->where('user_id', $coreUserId)
            ->where('app_code', $appCode)
            ->where('is_active', true)
            ->whereIn('role_slug', $allowedRoles)
            ->exists();
    }

    private function syncLocalAdminUser(object $coreUser): User
    {
        $email = $this->normalize((string) $coreUser->email);

        $user = User::query()->firstOrNew(['email' => $email]);
        $user->name = (string) $coreUser->name;
        $user->is_admin = true;

        if (! $user->exists) {
            $user->password = Hash::make(Str::random(64));
        }

        $user->save();

        return $user;
    }

    private function result(bool $ok, ?User $user, ?string $reason, string $via): array
    {
        return [
            'ok' => $ok,
            'user' => $user,
            'reason' => $reason,
            'via' => $via,
        ];
    }

    private function normalize(string $value): string
    {
        return strtolower(trim($value));
    }
}
