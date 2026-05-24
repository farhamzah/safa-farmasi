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
@endphp

<article class="group flex min-h-80 flex-col overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-teal-300 hover:shadow-md">
    <div class="relative flex h-32 items-center justify-center bg-gradient-to-br from-teal-50 via-white to-emerald-50">
        @if ($application->thumbnail_url)
            <img src="{{ $application->thumbnail_url }}" alt="{{ $application->name }}" class="h-full w-full object-cover">
        @else
            <span class="flex h-16 w-16 items-center justify-center rounded-lg bg-teal-700 text-xl font-bold text-white shadow-sm">
                {{ str($application->short_name ?: $application->name)->substr(0, 2)->upper() }}
            </span>
        @endif

        @if ($application->is_featured)
            <span class="absolute left-3 top-3 rounded-lg bg-white px-2.5 py-1 text-xs font-semibold text-teal-700 shadow-sm ring-1 ring-teal-100">
                Unggulan
            </span>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-5">
        <div class="flex flex-wrap gap-2">
            @forelse ($application->categories as $category)
                <span class="rounded-lg bg-teal-50 px-2.5 py-1 text-xs font-semibold text-teal-700">{{ $category->name }}</span>
            @empty
                <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">Layanan</span>
            @endforelse
            <span class="rounded-lg px-2.5 py-1 text-xs font-semibold ring-1 {{ $statusMeta['class'] }}">{{ $statusMeta['label'] }}</span>
        </div>

        <h3 class="mt-4 text-lg font-bold text-slate-950 group-hover:text-teal-800">{{ $application->name }}</h3>

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
                    class="inline-flex w-full justify-center rounded-lg bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2"
                    aria-label="Buka {{ $application->name }}"
                >
                    {{ $actionLabel }}
                </a>
            @else
                <button type="button" class="w-full cursor-not-allowed rounded-lg bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-500" disabled aria-disabled="true">
                    {{ $actionLabel }}
                </button>
            @endif
        </div>
    </div>
</article>
