<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Top Klik Aplikasi
        </x-slot>

        @php
            $topApplications = $this->getTopApplications();
        @endphp

        @if ($topApplications->isEmpty())
            <p class="text-sm text-gray-500">
                Belum ada data klik aplikasi. Data akan muncul setelah pengunjung membuka aplikasi dari landing page.
            </p>
        @else
            <div class="space-y-3">
                @foreach ($topApplications as $application)
                    <div class="flex items-center justify-between gap-4 rounded-lg border border-gray-200 px-4 py-3">
                        <span class="min-w-0 truncate text-sm font-semibold text-gray-700">{{ $application->application_name }}</span>
                        <span class="shrink-0 rounded-lg bg-primary-50 px-3 py-1 text-sm font-semibold text-primary-700">
                            {{ $application->total_clicks }} klik
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
