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

    $accentColor = $application->accent_color ?: '#0f766e';
@endphp

<article class="group relative flex min-h-96 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:border-emerald-200 hover:shadow-xl hover:shadow-emerald-950/10">
    <div class="h-1.5 w-full" style="background-color: {{ $accentColor }}"></div>

    <div class="relative flex h-36 items-center justify-center overflow-hidden bg-[#f2fbf7]">
        @if ($application->thumbnail_url)
            <img src="{{ $application->thumbnail_url }}" alt="{{ $application->name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
        @else
            <span class="flex h-20 w-20 items-center justify-center rounded-2xl bg-teal-700 text-2xl font-black text-white shadow-lg shadow-teal-900/20">
                {{ str($application->short_name ?: $application->name)->substr(0, 2)->upper() }}
            </span>
        @endif

        <div class="absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-white/95 to-white/0"></div>

        @if ($application->is_featured)
            <span class="absolute left-4 top-4 rounded-full bg-white px-3 py-1 text-xs font-black text-teal-700 shadow-sm ring-1 ring-emerald-100">
                Unggulan
            </span>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-5">
        <div class="flex flex-wrap gap-2">
            @forelse ($application->categories as $category)
                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-teal-700 ring-1 ring-emerald-100">{{ $category->name }}</span>
            @empty
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">Layanan</span>
            @endforelse
            <span class="rounded-full px-3 py-1 text-xs font-bold ring-1 {{ $statusMeta['class'] }}">{{ $statusMeta['label'] }}</span>
        </div>

        <h3 class="mt-4 text-xl font-black leading-snug text-slate-950 group-hover:text-teal-800">{{ $application->name }}</h3>

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
                    class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-slate-950 px-4 text-sm font-black text-white transition hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2"
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
