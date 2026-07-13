<?php

namespace App\Filament\Resources\LandingSettings\Schemas;

use App\Models\LandingSetting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class LandingSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Setting')
                    ->description('Kelola konten landing page. Teks, tombol, gambar hero, dan poin keunggulan bisa diubah dari sini.')
                    ->schema([
                        Select::make('group')
                            ->label('Group')
                            ->options([
                                'general' => 'General',
                                'hero' => 'Hero',
                                'values' => 'Values',
                                'services' => 'Services',
                                'about' => 'About',
                                'news' => 'News',
                                'contact' => 'Contact',
                                'footer' => 'Footer',
                            ])
                            ->default('general')
                            ->required(),
                        Select::make('type')
                            ->label('Type')
                            ->options([
                                'text' => 'Text',
                                'textarea' => 'Textarea',
                                'email' => 'Email',
                                'url' => 'URL',
                                'phone' => 'Phone',
                                'image' => 'Image',
                            ])
                            ->default('text')
                            ->live()
                            ->required(),
                        TextInput::make('key')
                            ->label('Key')
                            ->helperText('Contoh: site_name, hero_title, hero_image_url, value_1_title, services_title, about_title, news_title, contact_email, footer_text.')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('value')
                            ->label('Value')
                            ->helperText('Isi nilai setting. Boleh kosong jika ingin memakai fallback.')
                            ->visible(fn (Get $get): bool => ! in_array($get('type'), ['textarea', 'image'], true))
                            ->dehydrated(fn (Get $get): bool => ! in_array($get('type'), ['textarea', 'image'], true))
                            ->rules(fn (Get $get): array => match ($get('type')) {
                                'email' => ['nullable', 'email'],
                                'url' => ['nullable', 'url'],
                                'phone' => ['nullable', 'regex:/^[0-9+() .-]+$/'],
                                default => ['nullable', 'string'],
                            })
                            ->maxLength(255),
                        FileUpload::make('value')
                            ->label('Gambar')
                            ->helperText('Untuk hero_image_url: gambar hero bagian kanan atas landing. Gunakan JPG, PNG, atau WebP. Maksimal 4 MB.')
                            ->visible(fn (Get $get): bool => $get('type') === 'image')
                            ->dehydrated(fn (Get $get): bool => $get('type') === 'image')
                            ->image()
                            ->imagePreviewHeight('220')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(4096)
                            ->disk('public')
                            ->directory('safa/landing')
                            ->visibility('public')
                            ->multiple(false)
                            ->openable()
                            ->downloadable()
                            ->afterStateUpdated(function (FileUpload $component): void {
                                $files = collect($component->getRawState())
                                    ->filter(fn (mixed $file): bool => filled($file));

                                if ($files->count() <= 1) {
                                    return;
                                }

                                $component->rawState([
                                    $files->keys()->last() => $files->last(),
                                ]);
                            })
                            ->dehydrateStateUsing(fn (mixed $state): ?string => LandingSetting::normalizePublicAssetPathValue($state))
                            ->columnSpanFull(),
                        Textarea::make('value')
                            ->label('Value')
                            ->helperText('Textarea cocok untuk hero_description atau teks panjang lain.')
                            ->visible(fn (Get $get): bool => $get('type') === 'textarea')
                            ->dehydrated(fn (Get $get): bool => $get('type') === 'textarea')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
