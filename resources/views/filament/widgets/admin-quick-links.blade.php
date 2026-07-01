<x-filament-widgets::widget>
    <x-filament::section class="safa-admin-section">
        <x-slot name="heading">
            Akses Cepat
        </x-slot>

        <x-slot name="description">
            Jalur pintas untuk pekerjaan admin yang paling sering dilakukan.
        </x-slot>

        @php
            $links = [
                ['label' => 'Kelola Aplikasi', 'desc' => 'Tambah, urutkan, dan atur status layanan.', 'href' => url('/admin/portal-applications'), 'tone' => 'primary'],
                ['label' => 'Kelola Kategori', 'desc' => 'Rapikan struktur direktori portal.', 'href' => url('/admin/app-categories'), 'tone' => 'emerald'],
                ['label' => 'Pengumuman', 'desc' => 'Publikasikan info penting fakultas.', 'href' => url('/admin/announcements'), 'tone' => 'sky'],
                ['label' => 'Pengaturan Landing', 'desc' => 'Edit identitas, hero, dan kontak.', 'href' => url('/admin/landing-settings'), 'tone' => 'amber'],
                ['label' => 'Export Klik', 'desc' => 'Unduh data aktivitas akses pengguna.', 'href' => route('admin.exports.clicks'), 'tone' => 'slate'],
                ['label' => 'Lihat Landing Page', 'desc' => 'Buka tampilan publik di tab baru.', 'href' => url('/'), 'tone' => 'dark', 'external' => true],
            ];
        @endphp

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($links as $link)
                <a
                    href="{{ $link['href'] }}"
                    @if ($link['external'] ?? false) target="_blank" rel="noopener noreferrer" @endif
                    class="safa-admin-action group rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-primary-300 hover:shadow-lg hover:shadow-primary-950/5"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-black text-gray-950 group-hover:text-primary-700">{{ $link['label'] }}</p>
                            <p class="mt-1 text-xs leading-5 text-gray-500">{{ $link['desc'] }}</p>
                        </div>
                        <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-primary-500"></span>
                    </div>
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
