@php
    $statusMeta = match ($application->status) {
        'active' => ['label' => 'Aktif', 'class' => 'bg-emerald-50 text-emerald-700 ring-emerald-200'],
        'internal' => ['label' => 'Internal', 'class' => 'bg-sky-50 text-sky-700 ring-sky-200'],
        'maintenance' => ['label' => 'Maintenance', 'class' => 'bg-amber-50 text-amber-700 ring-amber-200'],
        'coming_soon' => ['label' => 'Segera Hadir', 'class' => 'bg-slate-100 text-slate-600 ring-slate-200'],
        default => ['label' => 'Nonaktif', 'class' => 'bg-slate-100 text-slate-600 ring-slate-200'],
    };

    $actionLabel = match ($application->status) {
        'maintenance' => 'Maintenance',
        'coming_soon' => 'Segera Hadir',
        default => $application->display_button_label,
    };

    $accentColor = $application->accent_color ?: '#082b5f';
    $primaryCategory = $application->categories->pluck('name')->first() ?: 'Layanan';
    $initial = str($application->short_name ?: $application->name)->substr(0, 2)->upper();
@endphp

<article class="group relative flex min-h-96 flex-col overflow-hidden rounded-2xl border border-sky-100 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:border-amber-200 hover:shadow-xl hover:shadow-blue-950/10">
    <div class="h-1.5 w-full" style="background-color: {{ $accentColor }}"></div>

    <div class="relative overflow-hidden bg-gradient-to-br from-sky-50 via-white to-blue-50 p-5">
        <div class="absolute right-0 top-0 h-24 w-24 rounded-bl-full bg-white/70"></div>
        <div class="absolute -bottom-10 -right-8 h-28 w-28 rounded-full bg-blue-950/5"></div>

        <div class="relative flex min-h-24 items-center justify-between gap-4">
            <span class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-blue-950 text-xl font-black text-white shadow-lg shadow-blue-950/15">
                @if ($application->thumbnail_url)
                    <span class="flex h-full w-full items-center justify-center bg-white p-2">
                        <img src="{{ $application->thumbnail_url }}" alt="{{ $application->name }}" class="h-full w-full object-contain">
                    </span>
                @else
                    {{ $initial }}
                @endif
            </span>

            <div class="min-w-0 text-right">
                <p class="truncate text-xs font-black uppercase tracking-wide text-blue-900/70">{{ $primaryCategory }}</p>
                <p class="mt-1 text-sm font-black text-blue-950">{{ $statusMeta['label'] }}</p>
            </div>
        </div>

        @if ($application->is_featured)
            <span class="absolute bottom-4 left-5 rounded-full bg-white px-3 py-1 text-xs font-black text-blue-900 shadow-sm ring-1 ring-amber-200">
                Unggulan
            </span>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-5">
        <div class="flex flex-wrap gap-2">
            @forelse ($application->categories as $category)
                <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-bold text-blue-900 ring-1 ring-sky-100">{{ $category->name }}</span>
            @empty
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">Layanan</span>
            @endforelse
            <span class="rounded-full px-3 py-1 text-xs font-bold ring-1 {{ $statusMeta['class'] }}">{{ $statusMeta['label'] }}</span>
        </div>

        <h3 class="mt-4 text-xl font-black leading-snug text-blue-950 group-hover:text-amber-600">{{ $application->name }}</h3>

        @if ($application->display_description)
            <p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-600">{{ $application->display_description }}</p>
        @else
            <p class="mt-3 text-sm leading-6 text-slate-500">Layanan digital Fakultas Farmasi UBP Karawang.</p>
        @endif

        <div class="mt-auto pt-5">
            @if ($application->is_linkable)
                <a
                    href="{{ route('applications.go', $application) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-blue-950 px-4 text-sm font-black text-white transition hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2"
                    aria-label="Buka {{ $application->name }}"
                >
                    {{ $actionLabel }}
                </a>
            @else
                <button type="button" class="min-h-11 w-full cursor-not-allowed rounded-xl bg-slate-100 px-4 text-sm font-black text-slate-500" disabled aria-disabled="true">
                    {{ $actionLabel }}
                </button>
            @endif
        </div>
    </div>
</article>
