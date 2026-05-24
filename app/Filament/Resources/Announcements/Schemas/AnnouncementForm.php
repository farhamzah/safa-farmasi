<?php

namespace App\Filament\Resources\Announcements\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konten Pengumuman')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255),
                        Select::make('type')
                            ->label('Tipe')
                            ->options([
                                'info' => 'Info',
                                'success' => 'Success',
                                'warning' => 'Warning',
                                'danger' => 'Danger',
                            ])
                            ->default('info')
                            ->required(),
                        Textarea::make('body')
                            ->label('Message')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('url')
                            ->label('Link')
                            ->helperText('Opsional. Jika diisi, link detail akan muncul pada banner pengumuman.')
                            ->url(),
                    ])
                    ->columns(2),
                Section::make('Jadwal Tampil')
                    ->schema([
                        DateTimePicker::make('starts_at')
                            ->label('Start at'),
                        DateTimePicker::make('ends_at')
                            ->label('End at')
                            ->rule('after_or_equal:starts_at'),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}
