<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Akses Cepat
        </x-slot>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
            <a href="{{ url('/admin/portal-applications') }}" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-700 transition hover:border-primary-500 hover:text-primary-600">
                Kelola Aplikasi
            </a>
            <a href="{{ url('/admin/app-categories') }}" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-700 transition hover:border-primary-500 hover:text-primary-600">
                Kelola Kategori
            </a>
            <a href="{{ url('/admin/announcements') }}" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-700 transition hover:border-primary-500 hover:text-primary-600">
                Pengumuman
            </a>
            <a href="{{ url('/admin/landing-settings') }}" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-700 transition hover:border-primary-500 hover:text-primary-600">
                Pengaturan Landing
            </a>
            <a href="{{ route('admin.exports.clicks') }}" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-700 transition hover:border-primary-500 hover:text-primary-600">
                Export Klik
            </a>
            <a href="{{ url('/') }}" target="_blank" rel="noopener noreferrer" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-700 transition hover:border-primary-500 hover:text-primary-600">
                Lihat Landing Page
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
