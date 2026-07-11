<?php

namespace App\Filament\Resources\LandingSettings\Schemas;

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
                    ->description('Kelola nilai teks landing page. Jika value kosong, landing tetap memakai fallback bawaan.')
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
                            ->visible(fn (Get $get): bool => $get('type') !== 'textarea')
                            ->dehydrated(fn (Get $get): bool => $get('type') !== 'textarea')
                            ->rules(fn (Get $get): array => match ($get('type')) {
                                'email' => ['nullable', 'email'],
                                'url' => ['nullable', 'url'],
                                'phone' => ['nullable', 'regex:/^[0-9+() .-]+$/'],
                                default => ['nullable', 'string'],
                            })
                            ->maxLength(255),
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
