<x-filament-widgets::widget>
    <x-filament::section class="safa-admin-section">
        <x-slot name="heading">
            Top Klik Aplikasi
        </x-slot>

        <x-slot name="description">
            Layanan yang paling sering dibuka dari landing page SAFA.
        </x-slot>

        @php
            $topApplications = $this->getTopApplications();
            $maxClicks = max(1, (int) $topApplications->max('total_clicks'));
        @endphp

        @if ($topApplications->isEmpty())
            <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-6">
                <p class="text-sm font-semibold text-gray-700">Belum ada data klik aplikasi.</p>
                <p class="mt-1 text-sm text-gray-500">Data akan muncul setelah pengunjung membuka aplikasi dari landing page.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($topApplications as $application)
                    @php
                        $percentage = max(8, ((int) $application->total_clicks / $maxClicks) * 100);
                    @endphp
                    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center justify-between gap-4">
                            <span class="min-w-0 truncate text-sm font-black text-gray-800">{{ $application->application_name }}</span>
                            <span class="shrink-0 rounded-full bg-primary-50 px-3 py-1 text-xs font-black text-primary-700 ring-1 ring-primary-100">
                                {{ $application->total_clicks }} klik
                            </span>
                        </div>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full rounded-full bg-primary-600" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
