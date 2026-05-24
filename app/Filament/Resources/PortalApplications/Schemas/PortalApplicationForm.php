<?php

namespace App\Filament\Resources\PortalApplications\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PortalApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Aplikasi')
                    ->description('Data utama yang tampil pada kartu aplikasi di landing page.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama aplikasi')
                            ->helperText('Nama layanan yang akan dilihat pengunjung.')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                        TextInput::make('slug')
                            ->helperText('Dipakai sebagai identifier unik. Boleh diedit manual jika diperlukan.')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('short_name')
                            ->label('Singkatan')
                            ->helperText('Dipakai sebagai fallback ikon jika thumbnail belum diunggah.')
                            ->maxLength(20),
                        Select::make('categories')
                            ->label('Kategori')
                            ->helperText('Aplikasi bisa berada pada lebih dari satu kategori.')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2),
                Section::make('Konten Kartu')
                    ->schema([
                        Textarea::make('short_description')
                            ->label('Deskripsi singkat')
                            ->helperText('Wajib, ringkas, dan langsung menjelaskan fungsi aplikasi.')
                            ->required()
                            ->maxLength(180)
                            ->rows(2)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),
                        Textarea::make('long_description')
                            ->label('Deskripsi panjang')
                            ->helperText('Opsional untuk kebutuhan detail lanjutan.')
                            ->rows(4)
                            ->columnSpanFull(),
                        FileUpload::make('thumbnail_path')
                            ->label('Thumbnail')
                            ->helperText('JPG, JPEG, PNG, atau WebP. Maksimal 2 MB.')
                            ->image()
                            ->imagePreviewHeight('160')
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                            ->maxSize(2048)
                            ->disk('public')
                            ->directory('application-thumbnails')
                            ->visibility('public'),
                    ]),
                Section::make('Akses dan Status')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'active' => 'Aktif',
                                'internal' => 'Internal',
                                'maintenance' => 'Maintenance',
                                'coming_soon' => 'Segera Hadir',
                                'inactive' => 'Nonaktif',
                            ])
                            ->helperText('Inactive dan aplikasi tidak aktif tidak tampil di landing page.')
                            ->required()
                            ->live()
                            ->default('inactive'),
                        TextInput::make('url')
                            ->label('Link tujuan')
                            ->helperText('Wajib untuk status Aktif dan Internal.')
                            ->url()
                            ->required(fn (Get $get): bool => in_array($get('status'), ['active', 'internal'], true)),
                        TextInput::make('button_label')
                            ->label('Label tombol')
                            ->helperText('Default: Masuk.')
                            ->default('Masuk')
                            ->maxLength(255),
                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->helperText('Angka kecil tampil lebih awal.')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_featured')
                            ->label('Unggulan')
                            ->helperText('Aplikasi unggulan tampil lebih dulu.')
                            ->default(false),
                        Toggle::make('is_active')
                            ->label('Aktif tampil')
                            ->helperText('Matikan untuk menyembunyikan aplikasi tanpa menghapus data.')
                            ->default(true),
                        Toggle::make('open_in_new_tab')
                            ->label('Buka di tab baru')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}
