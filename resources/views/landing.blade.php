@php
    $siteName = setting('site_name', 'SAFA UBP');
    $siteSubtitle = setting('site_subtitle', 'Satu Akses Farmasi UBP');
    $metaDescription = setting('hero_description', setting('subheadline', 'Portal satu halaman untuk layanan digital Fakultas Farmasi UBP Karawang.'));
    $siteLogo = setting('site_logo', '/images/logo-fakultas-farmasi-ubp.png');
    $siteFavicon = setting('site_favicon', '/favicon.png');
    $absoluteAsset = function (?string $path): ?string {
        if (blank($path)) {
            return null;
        }

        return \Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])
            ? $path
            : url($path);
    };
    $siteLogoUrl = $absoluteAsset($siteLogo);
    $siteFaviconUrl = $absoluteAsset($siteFavicon);
    $heroApplications = $applications->take(4);
    $featuredApplications = $applications->where('is_featured', true)->take(3);
    $primaryApplication = $applications->first(fn ($application) => $application->is_linkable);
    $activeApplicationCount = $applications
        ->filter(fn ($application) => in_array($application->status, ['active', 'internal'], true))
        ->count();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="theme-color" content="#0f766e">
    <meta property="og:title" content="{{ $siteName }} - {{ $siteSubtitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @if ($siteLogoUrl)
        <meta property="og:image" content="{{ $siteLogoUrl }}">
    @endif

    <title>{{ $siteName }} - {{ $siteSubtitle }}</title>
    @if ($siteFaviconUrl)
        <link rel="icon" type="image/png" href="{{ $siteFaviconUrl }}">
        <link rel="apple-touch-icon" href="{{ $siteFaviconUrl }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f7faf8] text-slate-950 antialiased">
    <header class="sticky top-0 z-40 border-b border-emerald-100/80 bg-white/90 shadow-sm shadow-emerald-950/5 backdrop-blur-xl">
        <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-3 sm:px-8 lg:px-10" aria-label="Navigasi utama">
            <a href="#beranda" class="flex min-w-0 items-center gap-3" aria-label="Kembali ke Beranda SAFA UBP">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-white text-sm font-bold text-teal-800 shadow-sm ring-1 ring-emerald-100">
                    @if ($siteLogoUrl)
                        <img src="{{ $siteLogoUrl }}" alt="{{ $siteName }}" class="h-full w-full object-contain p-1">
                    @else
                        SA
                    @endif
                </span>
                <span class="min-w-0">
                    <span class="block truncate text-base font-black text-teal-950">{{ $siteName }}</span>
                    <span class="hidden text-xs text-slate-500 sm:block">{{ $siteSubtitle }}</span>
                </span>
            </a>

            <div class="hidden items-center rounded-full border border-slate-200 bg-slate-50/80 p-1 text-sm font-bold text-slate-600 md:flex">
                <a href="#beranda" class="rounded-full px-4 py-2 transition hover:bg-white hover:text-teal-800 hover:shadow-sm">Beranda</a>
                <a href="#aplikasi" class="rounded-full px-4 py-2 transition hover:bg-white hover:text-teal-800 hover:shadow-sm">Aplikasi</a>
                <a href="#pengumuman" class="rounded-full px-4 py-2 transition hover:bg-white hover:text-teal-800 hover:shadow-sm">Pengumuman</a>
                <a href="#bantuan" class="rounded-full px-4 py-2 transition hover:bg-white hover:text-teal-800 hover:shadow-sm">Bantuan</a>
            </div>

            <a href="#aplikasi" class="hidden rounded-full bg-teal-700 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2 sm:inline-flex">
                Buka Portal
            </a>
        </nav>
    </header>

    <main id="beranda">
        <section class="overflow-hidden border-b border-emerald-100 bg-white">
            <div class="mx-auto grid max-w-7xl gap-10 px-5 py-10 sm:px-8 md:py-14 lg:grid-cols-[1.04fr_0.96fr] lg:px-10 lg:py-16">
                <div class="flex flex-col justify-center">
                    <div class="inline-flex w-fit items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-bold uppercase tracking-wide text-teal-800">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Portal resmi Fakultas Farmasi UBP
                    </div>
                    <h1 class="mt-5 max-w-4xl text-4xl font-black leading-[1.04] text-slate-950 sm:text-5xl lg:text-6xl">
                        {{ setting('hero_title', setting('headline')) }}
                    </h1>
                    <p class="mt-5 max-w-2xl text-base leading-8 text-slate-600 sm:text-lg">
                        {{ setting('hero_description', setting('subheadline')) }}
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <a href="#aplikasi" class="inline-flex min-h-12 items-center justify-center rounded-xl bg-teal-700 px-6 text-sm font-bold text-white shadow-lg shadow-teal-900/15 transition hover:-translate-y-0.5 hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2">
                            Lihat Aplikasi
                        </a>
                        @if ($primaryApplication)
                            <a href="{{ route('applications.go', $primaryApplication) }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-slate-200 bg-white px-6 text-sm font-bold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-300 hover:text-teal-800">
                                Masuk {{ $primaryApplication->short_name ?: $primaryApplication->name }}
                            </a>
                        @endif
                    </div>

                    <div class="mt-8 grid max-w-xl grid-cols-3 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="p-4">
                            <p class="text-2xl font-black text-slate-950">{{ $activeApplicationCount }}</p>
                            <p class="mt-1 text-xs font-medium text-slate-500">Layanan aktif</p>
                        </div>
                        <div class="border-x border-slate-200 p-4">
                            <p class="text-2xl font-black text-slate-950">{{ $categories->count() }}</p>
                            <p class="mt-1 text-xs font-medium text-slate-500">Kategori</p>
                        </div>
                        <div class="p-4">
                            <p class="text-2xl font-black text-slate-950">{{ $announcements->count() }}</p>
                            <p class="mt-1 text-xs font-medium text-slate-500">Info terbaru</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center">
                    <div class="w-full rounded-[1.75rem] border border-emerald-100 bg-[#f2fbf7] p-3 shadow-2xl shadow-emerald-950/10">
                        <div class="rounded-[1.35rem] border border-white/80 bg-white p-5 shadow-sm">
                            <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-5">
                                <div class="flex items-center gap-3">
                                    @if ($siteLogoUrl)
                                        <span class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-emerald-100">
                                            <img src="{{ $siteLogoUrl }}" alt="{{ $siteName }}" class="h-full w-full object-contain p-1">
                                        </span>
                                    @endif
                                    <div>
                                        <p class="text-base font-black text-slate-950">Portal Layanan</p>
                                        <p class="text-sm text-slate-500">Akses cepat aplikasi fakultas</p>
                                    </div>
                                </div>
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-teal-700 ring-1 ring-emerald-200">Online</span>
                            </div>

                            <div class="mt-5 space-y-3">
                                @forelse ($heroApplications as $application)
                                    @php
                                        $categoryName = $application->categories->pluck('name')->first() ?: 'Layanan';
                                        $statusLabel = match ($application->status) {
                                            'maintenance' => 'Maintenance',
                                            'coming_soon' => 'Segera',
                                            'internal' => 'Internal',
                                            default => 'Aktif',
                                        };
                                    @endphp
                                    <div class="group flex items-center gap-3 rounded-2xl border border-slate-100 bg-slate-50/70 p-3 transition hover:border-emerald-200 hover:bg-white hover:shadow-md hover:shadow-emerald-950/5">
                                        <span class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-teal-700 text-sm font-black text-white shadow-sm">
                                            @if ($application->thumbnail_url)
                                                <img src="{{ $application->thumbnail_url }}" alt="{{ $application->name }}" class="h-full w-full object-cover">
                                            @else
                                                {{ str($application->short_name ?: $application->name)->substr(0, 2)->upper() }}
                                            @endif
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-black text-slate-950">{{ $application->name }}</p>
                                            <p class="mt-0.5 truncate text-xs font-medium text-slate-500">{{ $categoryName }}</p>
                                        </div>
                                        <span class="rounded-full bg-white px-2.5 py-1 text-[11px] font-bold text-teal-700 ring-1 ring-emerald-100">{{ $statusLabel }}</span>
                                    </div>
                                @empty
                                    <div class="rounded-2xl border border-dashed border-emerald-200 bg-emerald-50 p-5 text-sm text-slate-600">
                                        Layanan akan tampil di sini setelah ditambahkan admin.
                                    </div>
                                @endforelse
                            </div>

                            @if ($featuredApplications->isNotEmpty())
                                <div class="mt-5 rounded-2xl bg-slate-950 p-4 text-white">
                                    <p class="text-xs font-bold uppercase tracking-wide text-emerald-200">Jalur cepat</p>
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @foreach ($featuredApplications as $application)
                                            @if ($application->is_linkable)
                                                <a href="{{ route('applications.go', $application) }}" target="_blank" rel="noopener noreferrer" class="rounded-full bg-white/10 px-3 py-2 text-xs font-bold transition hover:bg-white hover:text-slate-950">
                                                    {{ $application->short_name ?: $application->name }}
                                                </a>
                                            @else
                                                <span class="rounded-full bg-white/10 px-3 py-2 text-xs font-bold text-white/65">
                                                    {{ $application->short_name ?: $application->name }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="mx-auto flex max-w-7xl gap-2 overflow-x-auto px-5 pb-6 sm:hidden">
                <a href="#aplikasi" class="shrink-0 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700">Aplikasi</a>
                <a href="#pengumuman" class="shrink-0 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700">Pengumuman</a>
                <a href="#bantuan" class="shrink-0 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700">Bantuan</a>
            </div>
        </section>

        <section id="pengumuman" class="mx-auto max-w-7xl scroll-mt-24 px-5 py-10 sm:px-8 lg:px-10" aria-labelledby="pengumuman-heading">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wide text-teal-700">Informasi Terkini</p>
                    <h2 id="pengumuman-heading" class="mt-2 text-3xl font-black text-slate-950">Pengumuman</h2>
                </div>
                <p class="max-w-md text-sm leading-6 text-slate-500">Info penting fakultas tampil ringkas agar mahasiswa dan staf langsung tahu prioritas terbaru.</p>
            </div>

            @if ($announcements->isNotEmpty())
                <div class="mt-6 grid gap-4 lg:grid-cols-3">
                    @foreach ($announcements as $announcement)
                        @php
                            $announcementClass = match ($announcement->type) {
                                'success' => 'border-emerald-200 bg-emerald-50 text-emerald-900',
                                'warning' => 'border-amber-200 bg-amber-50 text-amber-900',
                                'danger' => 'border-rose-200 bg-rose-50 text-rose-900',
                                default => 'border-sky-200 bg-sky-50 text-sky-900',
                            };
                        @endphp
                        <div class="flex min-h-40 flex-col rounded-2xl border p-5 shadow-sm {{ $announcementClass }}">
                            <div class="flex items-start justify-between gap-4">
                                <p class="text-base font-black">{{ $announcement->title }}</p>
                                <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-current opacity-60"></span>
                            </div>
                            @if ($announcement->body)
                                <p class="mt-3 line-clamp-4 text-sm leading-6 opacity-85">{{ $announcement->body }}</p>
                            @endif
                            @if ($announcement->url)
                                <a href="{{ $announcement->url }}" target="_blank" rel="noopener noreferrer" class="mt-auto pt-4 text-sm font-black underline underline-offset-4" aria-label="Buka detail pengumuman {{ $announcement->title }}">
                                    Detail
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="mt-6 rounded-2xl border border-dashed border-emerald-200 bg-white p-6 text-sm text-slate-600 shadow-sm">
                    Belum ada pengumuman aktif.
                </div>
            @endif
        </section>

        <section id="aplikasi" class="border-y border-slate-200 bg-white">
            <div class="mx-auto max-w-7xl px-5 py-12 sm:px-8 lg:px-10">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-wide text-teal-700">Direktori Aplikasi</p>
                        <h2 class="mt-2 text-3xl font-black text-slate-950">Pilih layanan yang dibutuhkan</h2>
                    </div>
                    <p class="max-w-sm text-sm leading-6 text-slate-500">Cari layanan, pilih kategori, lalu masuk ke aplikasi resmi dari satu halaman.</p>
                </div>

                <form action="{{ route('landing') }}#aplikasi" method="GET" class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-3 shadow-sm">
                    @if ($selectedCategory !== '')
                        <input type="hidden" name="category" value="{{ $selectedCategory }}">
                    @endif
                    <div class="grid gap-3 md:grid-cols-[1fr_auto]">
                        <label class="sr-only" for="q">Cari aplikasi</label>
                        <input
                            id="q"
                            name="q"
                            value="{{ $search }}"
                            type="search"
                            placeholder="Cari aplikasi, layanan, formulir..."
                            class="min-h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-teal-500 focus:ring-2 focus:ring-teal-100"
                        >
                        <button type="submit" class="min-h-12 rounded-xl bg-slate-950 px-6 text-sm font-bold text-white transition hover:bg-teal-800">
                            Cari
                        </button>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2" aria-label="Filter kategori">
                        <a href="{{ route('landing', array_filter(['q' => $search])) }}#aplikasi" class="rounded-full px-4 py-2 text-sm font-bold transition {{ $selectedCategory === '' ? 'bg-teal-700 text-white shadow-sm' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-emerald-50 hover:text-teal-800' }}">
                            Semua
                        </a>
                        @foreach ($categories as $category)
                            <a href="{{ route('landing', array_filter(['q' => $search, 'category' => $category->slug])) }}#aplikasi" class="rounded-full px-4 py-2 text-sm font-bold transition {{ $selectedCategory === $category->slug ? 'bg-teal-700 text-white shadow-sm' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-emerald-50 hover:text-teal-800' }}">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </form>

                @if ($applications->isNotEmpty())
                    <div class="mt-7 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($applications as $application)
                            @include('partials.application-card', ['application' => $application])
                        @endforeach
                    </div>
                @else
                    <div class="mt-7 rounded-2xl border border-dashed border-emerald-200 bg-white p-8 text-center shadow-sm">
                        <h3 class="text-lg font-semibold text-slate-950">Aplikasi tidak ditemukan.</h3>
                        <p class="mt-2 text-sm text-slate-600">Coba gunakan kata kunci lain atau pilih kategori Semua.</p>
                    </div>
                @endif
            </div>
        </section>

        <section id="bantuan">
            <div class="mx-auto max-w-7xl px-5 py-12 sm:px-8 lg:px-10">
                <div class="grid gap-6 rounded-[1.5rem] border border-slate-200 bg-slate-950 p-6 text-white shadow-xl shadow-slate-950/10 md:grid-cols-[1fr_auto] md:items-center md:p-8">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-wide text-emerald-200">Bantuan</p>
                        <h2 class="mt-2 text-3xl font-black">Butuh bantuan akses layanan?</h2>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">Hubungi kanal resmi fakultas bila akun, akses aplikasi, atau tautan layanan belum sesuai kebutuhan.</p>
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row md:justify-end">
                        @if (setting('contact_email'))
                            <a href="mailto:{{ setting('contact_email') }}" class="rounded-xl bg-white px-4 py-3 text-sm font-bold text-slate-950 transition hover:bg-emerald-50">
                                {{ setting('contact_email') }}
                            </a>
                        @endif
                        @if (setting('contact_whatsapp'))
                            <a href="https://wa.me/{{ preg_replace('/\D+/', '', setting('contact_whatsapp')) }}" target="_blank" rel="noopener noreferrer" class="rounded-xl bg-white px-4 py-3 text-sm font-bold text-slate-950 transition hover:bg-emerald-50">
                                WhatsApp {{ setting('contact_whatsapp') }}
                            </a>
                        @endif
                        @if (! setting('contact_email') && ! setting('contact_whatsapp'))
                            <p class="text-sm leading-6 text-slate-300">Hubungi Tata Usaha Fakultas Farmasi UBP untuk bantuan akses layanan.</p>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-2 px-5 py-6 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-8 lg:px-10">
            <span>{{ setting('footer_text') }} &copy; {{ now()->year }}</span>
            <span>{{ $siteName }} - {{ $siteSubtitle }}.</span>
        </div>
    </footer>
</body>
</html>
