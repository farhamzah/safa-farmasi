<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use LogicException;

/**
 * @property-read Schema $form
 */
class ChangePassword extends Page
{
    protected static ?string $title = 'Ubah Password';

    protected static ?string $slug = 'ubah-password';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static ?string $navigationLabel = 'Ubah Password';

    protected static ?int $navigationSort = 90;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public static function canAccess(): bool
    {
        return Filament::auth()->check();
    }

    public static function isTenantSubscriptionRequired(Panel $panel): bool
    {
        return false;
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ubah Password')
                    ->description('Gunakan password yang kuat dan jangan bagikan ke orang lain.')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Password saat ini')
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->autocomplete('current-password')
                            ->currentPassword(guard: Filament::getAuthGuard())
                            ->required()
                            ->dehydrated(false),
                        TextInput::make('password')
                            ->label('Password baru')
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->autocomplete('new-password')
                            ->required()
                            ->minLength(8)
                            ->same('password_confirmation'),
                        TextInput::make('password_confirmation')
                            ->label('Konfirmasi password baru')
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->autocomplete('new-password')
                            ->required()
                            ->dehydrated(false),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $password = (string) $data['password'];

        $user = $this->getUser();
        $hashedPassword = Hash::make($password);

        $user->forceFill([
            'password' => $hashedPassword,
        ])->save();

        if (request()->hasSession()) {
            request()->session()->put([
                'password_hash_' . Filament::getAuthGuard() => $hashedPassword,
            ]);
        }

        $this->form->fill();

        Notification::make()
            ->title('Password berhasil diubah')
            ->success()
            ->send();
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Simpan Password')
                                ->submit('save'),
                        ])->alignment(Alignment::Start),
                    ]),
            ]);
    }

    protected function getUser(): Authenticatable&Model
    {
        $user = Filament::auth()->user();

        if (! $user instanceof Model) {
            throw new LogicException('User yang login harus berupa model Eloquent agar password bisa diubah.');
        }

        return $user;
    }
}
