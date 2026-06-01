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
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/95 backdrop-blur">
        <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-3 sm:px-8 lg:px-10" aria-label="Navigasi utama">
            <a href="#beranda" class="flex min-w-0 items-center gap-3" aria-label="Kembali ke Beranda SAFA UBP">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-white text-sm font-bold text-teal-800 ring-1 ring-teal-100">
                    @if ($siteLogoUrl)
                        <img src="{{ $siteLogoUrl }}" alt="{{ $siteName }}" class="h-full w-full object-contain p-1">
                    @else
                        SA
                    @endif
                </span>
                <span class="min-w-0">
                    <span class="block truncate text-base font-bold text-teal-950">{{ $siteName }}</span>
                    <span class="hidden text-xs text-slate-500 sm:block">{{ $siteSubtitle }}</span>
                </span>
            </a>

            <div class="hidden items-center gap-6 text-sm font-medium text-slate-600 md:flex">
                <a href="#beranda" class="hover:text-teal-800">Beranda</a>
                <a href="#aplikasi" class="hover:text-teal-800">Aplikasi</a>
                <a href="#pengumuman" class="hover:text-teal-800">Pengumuman</a>
                <a href="#bantuan" class="hover:text-teal-800">Bantuan</a>
            </div>
        </nav>
    </header>

    <main id="beranda">
        <section class="border-b border-teal-100 bg-white">
            <div class="mx-auto grid max-w-7xl gap-8 px-5 py-10 sm:px-8 md:py-14 lg:grid-cols-[1.1fr_0.9fr] lg:px-10 lg:py-16">
                <div class="flex flex-col justify-center">
                    <p class="text-sm font-semibold uppercase tracking-wide text-teal-700">Fakultas Farmasi Universitas Buana Perjuangan Karawang</p>
                    <h1 class="mt-4 max-w-4xl text-4xl font-bold leading-tight text-teal-950 sm:text-5xl">
                        {{ setting('hero_title', setting('headline')) }}
                    </h1>
                    <p class="mt-5 max-w-2xl text-base leading-7 text-slate-600 sm:text-lg">
                        {{ setting('hero_description', setting('subheadline')) }}
                    </p>
                    <div class="mt-7 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <a href="#aplikasi" class="inline-flex justify-center rounded-lg bg-teal-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2">
                            Lihat Aplikasi
                        </a>
                        <span class="text-sm text-slate-500">{{ $applications->count() }} layanan tersedia</span>
                    </div>
                </div>

                <div class="flex items-center">
                    <div class="w-full rounded-lg border border-teal-100 bg-gradient-to-br from-teal-50 via-white to-emerald-50 p-5 shadow-sm">
                        <div class="flex items-center justify-between gap-4 border-b border-teal-100 pb-4">
                            <div class="flex items-center gap-3">
                                @if ($siteLogoUrl)
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-white ring-1 ring-teal-100">
                                        <img src="{{ $siteLogoUrl }}" alt="{{ $siteName }}" class="h-full w-full object-contain p-1">
                                    </span>
                                @endif
                                <div>
                                    <p class="text-sm font-semibold text-teal-900">Portal Layanan</p>
                                    <p class="text-xs text-slate-500">Akses cepat aplikasi fakultas</p>
                                </div>
                            </div>
                            <span class="rounded-lg bg-white px-3 py-1 text-xs font-semibold text-teal-700 ring-1 ring-teal-100">Online</span>
                        </div>
                        <div class="mt-5 grid grid-cols-2 gap-3">
                            @foreach ($applications->take(4) as $application)
                                <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-100">
                                    <span class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-lg bg-teal-700 text-sm font-bold text-white">
                                        @if ($application->thumbnail_url)
                                            <img src="{{ $application->thumbnail_url }}" alt="{{ $application->name }}" class="h-full w-full object-cover">
                                        @else
                                            {{ str($application->short_name ?: $application->name)->substr(0, 2)->upper() }}
                                        @endif
                                    </span>
                                    <p class="mt-3 truncate text-sm font-semibold text-slate-950">{{ $application->name }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $application->categories->pluck('name')->first() ?: 'Layanan' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="pengumuman" class="mx-auto max-w-7xl scroll-mt-24 px-5 pt-6 sm:px-8 lg:px-10" aria-labelledby="pengumuman-heading">
            <div class="mb-4">
                <p class="text-sm font-semibold uppercase tracking-wide text-teal-700">Informasi Terkini</p>
                <h2 id="pengumuman-heading" class="mt-2 text-2xl font-bold text-slate-950">Pengumuman</h2>
            </div>

            @if ($announcements->isNotEmpty())
                <div class="space-y-3">
                    @foreach ($announcements as $announcement)
                        @php
                            $announcementClass = match ($announcement->type) {
                                'success' => 'border-emerald-200 bg-emerald-50 text-emerald-900',
                                'warning' => 'border-amber-200 bg-amber-50 text-amber-900',
                                'danger' => 'border-rose-200 bg-rose-50 text-rose-900',
                                default => 'border-sky-200 bg-sky-50 text-sky-900',
                            };
                        @endphp
                        <div class="rounded-lg border px-4 py-3 text-sm {{ $announcementClass }}">
                            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="font-semibold">{{ $announcement->title }}</p>
                                    @if ($announcement->body)
                                        <p class="mt-1 leading-6 opacity-85">{{ $announcement->body }}</p>
                                    @endif
                                </div>
                                @if ($announcement->url)
                                    <a href="{{ $announcement->url }}" target="_blank" rel="noopener noreferrer" class="shrink-0 font-semibold underline underline-offset-4" aria-label="Buka detail pengumuman {{ $announcement->title }}">
                                        Detail
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-lg border border-dashed border-teal-200 bg-white p-5 text-sm text-slate-600">
                    Belum ada pengumuman aktif.
                </div>
            @endif
        </section>

        <section id="aplikasi" class="mx-auto max-w-7xl px-5 py-10 sm:px-8 lg:px-10">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-teal-700">Direktori Aplikasi</p>
                    <h2 class="mt-2 text-2xl font-bold text-slate-950">Pilih layanan yang dibutuhkan</h2>
                </div>
                <p class="text-sm text-slate-500">Gunakan pencarian atau filter kategori.</p>
            </div>

            <form action="{{ route('landing') }}#aplikasi" method="GET" class="mt-6 rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
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
                        class="min-h-12 w-full rounded-lg border border-slate-200 bg-slate-50 px-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-teal-500 focus:bg-white focus:ring-2 focus:ring-teal-100"
                    >
                    <button type="submit" class="min-h-12 rounded-lg bg-teal-700 px-5 text-sm font-semibold text-white transition hover:bg-teal-800">
                        Cari
                    </button>
                </div>

                <div class="mt-4 flex flex-wrap gap-2" aria-label="Filter kategori">
                    <a href="{{ route('landing', array_filter(['q' => $search])) }}#aplikasi" class="rounded-lg px-3 py-2 text-sm font-semibold transition {{ $selectedCategory === '' ? 'bg-teal-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-teal-50 hover:text-teal-800' }}">
                        Semua
                    </a>
                    @foreach ($categories as $category)
                        <a href="{{ route('landing', array_filter(['q' => $search, 'category' => $category->slug])) }}#aplikasi" class="rounded-lg px-3 py-2 text-sm font-semibold transition {{ $selectedCategory === $category->slug ? 'bg-teal-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-teal-50 hover:text-teal-800' }}">
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
                <div class="mt-7 rounded-lg border border-dashed border-teal-200 bg-white p-8 text-center">
                    <h3 class="text-lg font-semibold text-slate-950">Aplikasi tidak ditemukan.</h3>
                    <p class="mt-2 text-sm text-slate-600">Coba gunakan kata kunci lain atau pilih kategori Semua.</p>
                </div>
            @endif
        </section>

        <section id="bantuan" class="border-t border-slate-200 bg-white">
            <div class="mx-auto max-w-7xl px-5 py-10 sm:px-8 lg:px-10">
                <div class="rounded-lg bg-slate-50 p-6 ring-1 ring-slate-200">
                    <p class="text-sm font-semibold uppercase tracking-wide text-teal-700">Bantuan</p>
                    <h2 class="mt-2 text-2xl font-bold text-slate-950">Butuh bantuan akses layanan?</h2>
                    <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                        @if (setting('contact_email'))
                            <a href="mailto:{{ setting('contact_email') }}" class="rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-teal-300 hover:text-teal-800">
                                {{ setting('contact_email') }}
                            </a>
                        @endif
                        @if (setting('contact_whatsapp'))
                            <a href="https://wa.me/{{ preg_replace('/\D+/', '', setting('contact_whatsapp')) }}" target="_blank" rel="noopener noreferrer" class="rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-teal-300 hover:text-teal-800">
                                WhatsApp {{ setting('contact_whatsapp') }}
                            </a>
                        @endif
                        @if (! setting('contact_email') && ! setting('contact_whatsapp'))
                            <p class="text-sm leading-6 text-slate-600">Hubungi Tata Usaha Fakultas Farmasi UBP untuk bantuan akses layanan.</p>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-2 px-5 py-6 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-8 lg:px-10">
            <span>{{ setting('footer_text') }} © {{ now()->year }}</span>
            <span>{{ $siteName }} - {{ $siteSubtitle }}.</span>
        </div>
    </footer>
</body>
</html>
