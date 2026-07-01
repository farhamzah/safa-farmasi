<x-filament-widgets::widget>
    @php
        $summary = $this->getSummary();
        $summaryItems = [
            ['label' => 'Layanan tampil', 'value' => $summary['visible_applications']],
            ['label' => 'Unggulan', 'value' => $summary['featured_applications']],
            ['label' => 'Kategori aktif', 'value' => $summary['active_categories']],
            ['label' => 'Klik hari ini', 'value' => $summary['clicks_today']],
        ];
    @endphp

    <section class="safa-admin-hero overflow-hidden rounded-2xl border border-white/70 bg-slate-950 text-white shadow-xl shadow-slate-950/10">
        <div class="grid gap-6 p-6 lg:grid-cols-[1fr_auto] lg:items-end lg:p-7">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-emerald-300/25 bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-wide text-emerald-100">
                    <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
                    Admin Console
                </div>
                <h2 class="mt-4 max-w-3xl text-2xl font-black tracking-tight text-white sm:text-3xl">
                    Kendalikan Portal SAFA UBP dari satu dashboard.
                </h2>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
                    Pantau layanan, publikasi pengumuman, dan arahkan pengguna ke aplikasi fakultas tanpa berpindah konteks.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:min-w-[34rem]">
                @foreach ($summaryItems as $item)
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-2xl font-black text-white">{{ $item['value'] }}</p>
                        <p class="mt-1 text-xs font-medium text-slate-300">{{ $item['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-filament-widgets::widget>
