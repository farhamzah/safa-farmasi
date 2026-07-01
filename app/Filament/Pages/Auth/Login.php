<?php

namespace App\Filament\Pages\Auth;

use App\Services\CoreBridgeAuthService;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function authenticate(): ?LoginResponse
    {
        if (config('safa_auth.mode', 'local') === 'local') {
            return parent::authenticate();
        }

        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

        /** @var CoreBridgeAuthService $authBridge */
        $authBridge = app(CoreBridgeAuthService::class);
        $result = $authBridge->attempt(
            (string) ($data['email'] ?? ''),
            (string) ($data['password'] ?? ''),
            (bool) ($data['remember'] ?? false),
        );

        if (! $result['ok']) {
            $message = match ($result['reason']) {
                'core_password_must_change' => 'Password awal harus diganti dahulu melalui Core Profile Portal.',
                'core_app_access_denied' => 'Akun Core belum memiliki akses Admin SAFA.',
                'core_user_inactive' => 'Akun Core tidak aktif.',
                'core_unavailable' => 'Koneksi Core belum tersedia.',
                default => __('filament-panels::auth/pages/login.messages.failed'),
            };

            throw ValidationException::withMessages([
                'data.email' => $message,
            ]);
        }

        if (! Filament::auth()->check() || ! Filament::auth()->user()?->canAccessPanel(Filament::getCurrentOrDefaultPanel())) {
            Filament::auth()->logout();
            $this->throwFailureValidationException();
        }

        session()->regenerate();

        Notification::make()
            ->title('Login Admin SAFA berhasil')
            ->body('Akses diverifikasi melalui Core Farmasi.')
            ->success()
            ->send();

        return app(LoginResponse::class);
    }
}
