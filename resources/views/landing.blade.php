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

        $path = str_replace('\\', '/', trim($path));

        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (\Illuminate\Support\Str::startsWith($path, ['/storage/', 'storage/'])) {
            return url('/'.ltrim($path, '/'));
        }

        $publicPath = ltrim($path, '/');

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($publicPath)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($publicPath);
        }

        return url($path);
    };
    $siteLogoUrl = $absoluteAsset($siteLogo);
    $siteFaviconUrl = $absoluteAsset($siteFavicon);
    $heroImageUrl = $absoluteAsset(setting('hero_image_url', '/images/hero-farmasi-default.svg'));
    $showcase = ($showcaseApplications ?? $applications)->take(6);
    $programCards = ($programApplications ?? collect())->take(2);
    $primaryApplication = ($showcaseApplications ?? $applications)->first(fn ($application) => $application->is_linkable);
    $activeApplicationCount = ($showcaseApplications ?? $applications)
        ->filter(fn ($application) => in_array($application->status, ['active', 'internal'], true))
        ->count();
    $values = collect([1, 2, 3, 4])
        ->map(fn (int $index) => setting("value_{$index}_title"))
        ->filter()
        ->values();
    $credibleItems = collect(range(1, 8))
        ->map(fn (int $index) => setting("credible_{$index}_title"))
        ->filter()
        ->values();
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
<body class="min-h-screen bg-[#f5fbff] text-[#071b3b] antialiased">
    <header class="sticky top-0 z-40 border-b border-sky-100/80 bg-white/90 shadow-sm shadow-sky-950/5 backdrop-blur-xl">
        <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-4 sm:px-8 lg:px-10" aria-label="Navigasi utama">
            <a href="#beranda" class="flex min-w-0 items-center gap-3" aria-label="Kembali ke Beranda SAFA UBP">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-white text-sm font-black text-blue-900 ring-1 ring-sky-100">
                    @if ($siteLogoUrl)
                        <img src="{{ $siteLogoUrl }}" alt="{{ $siteName }}" class="h-full w-full object-contain p-1">
                    @else
                        SA
                    @endif
                </span>
                <span class="min-w-0">
                    <span class="block truncate text-sm font-black uppercase text-blue-950 sm:text-base">{{ $siteName }}</span>
                    <span class="hidden max-w-64 truncate text-xs font-semibold text-blue-800 sm:block">{{ $siteSubtitle }}</span>
                </span>
            </a>

            <div class="hidden items-center gap-7 text-sm font-black text-blue-950 lg:flex">
                <a href="#beranda" class="border-b-2 border-amber-400 pb-2 text-blue-900">Beranda</a>
                <a href="#tentang" class="pb-2 transition hover:text-amber-600">Tentang Kami</a>
                <a href="#layanan" class="pb-2 transition hover:text-amber-600">Layanan</a>
                <a href="#aplikasi" class="pb-2 transition hover:text-amber-600">Aplikasi</a>
                <a href="#pengumuman" class="pb-2 transition hover:text-amber-600">Berita</a>
                <a href="#kontak" class="pb-2 transition hover:text-amber-600">Kontak</a>
            </div>

            <a href="#aplikasi" class="inline-flex rounded-full border border-blue-900 px-4 py-2 text-xs font-black text-blue-950 transition hover:bg-blue-950 hover:text-white sm:px-5 sm:text-sm">
                Aplikasi
            </a>
        </nav>
    </header>

    <main id="beranda">
        <section class="relative overflow-hidden bg-white">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_76%_14%,rgba(186,230,253,0.68),transparent_25rem),linear-gradient(90deg,#ffffff_0%,#f5fbff_50%,#e0f2fe_100%)]"></div>
            <div class="absolute bottom-0 left-0 h-24 w-1/2 rounded-tr-full bg-amber-400/90"></div>

            <div class="relative mx-auto grid min-h-[35rem] max-w-7xl items-center gap-8 px-5 pb-12 pt-10 sm:px-8 lg:grid-cols-[0.88fr_1fr] lg:px-10 lg:pb-20 lg:pt-14">
                <div class="max-w-3xl">
                    <p class="text-sm font-black uppercase tracking-wide text-blue-900">{{ setting('hero_kicker') }}</p>
                    <h1 class="mt-5 break-words text-4xl font-black uppercase leading-[0.98] tracking-normal text-blue-950 sm:text-6xl lg:text-7xl">
                        {{ setting('hero_title') }}
                        <span class="block text-amber-400">{{ setting('hero_highlight') }}</span>
                    </h1>
                    <p class="mt-6 max-w-2xl text-base font-medium leading-8 text-blue-950/80 sm:text-lg">
                        {{ setting('hero_description', setting('subheadline')) }}
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <a href="#layanan" class="inline-flex min-h-12 w-full items-center justify-center gap-3 rounded-full bg-blue-950 px-6 text-sm font-black text-white shadow-xl shadow-blue-950/20 transition hover:-translate-y-0.5 hover:bg-blue-900 sm:w-auto">
                            {{ setting('hero_primary_button') }}
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white text-blue-950">-></span>
                        </a>
                        <a href="#aplikasi" class="inline-flex min-h-12 w-full items-center justify-center gap-3 rounded-full border border-blue-950 bg-white/70 px-6 text-sm font-black text-blue-950 transition hover:-translate-y-0.5 hover:bg-white sm:w-auto">
                            {{ setting('hero_secondary_button') }}
                            <span>-></span>
                        </a>
                    </div>
                </div>

                <div class="pointer-events-none relative min-h-[18rem] select-none sm:min-h-[23rem] lg:min-h-[31rem]" aria-label="Visual Fakultas Farmasi UBP">
                    <div class="absolute inset-0 overflow-hidden rounded-[2rem] shadow-2xl shadow-blue-950/12 ring-1 ring-white/80 lg:-right-10 lg:rounded-l-[2.5rem] lg:rounded-r-none">
                        <img src="{{ $heroImageUrl }}" alt="{{ setting('hero_title') }}" class="h-full w-full object-cover object-center">
                        <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(255,255,255,0.78)_0%,rgba(255,255,255,0.24)_31%,rgba(255,255,255,0)_58%)]"></div>
                        <div class="absolute bottom-0 left-0 right-0 h-28 bg-gradient-to-t from-white/60 to-transparent"></div>
                    </div>
                </div>
            </div>

            @if ($values->isNotEmpty())
                <div class="relative bg-blue-950">
                    <div class="absolute -top-8 left-0 h-12 w-1/3 rounded-tr-full bg-amber-400"></div>
                    <div class="mx-auto grid max-w-7xl gap-4 px-5 py-8 text-white sm:grid-cols-2 sm:px-8 lg:grid-cols-4 lg:px-10">
                        @foreach ($values as $value)
                            <div class="flex items-center gap-4">
                                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border border-amber-300/70 text-xl font-black text-amber-300">{{ str($value)->substr(0, 1)->upper() }}</span>
                                <p class="text-sm font-black leading-5">{{ $value }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </section>

        <section id="layanan" class="mx-auto max-w-7xl scroll-mt-24 px-5 py-12 sm:px-8 lg:px-10">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-black uppercase tracking-wide text-amber-500">-- {{ setting('services_eyebrow') }} --</p>
                <h2 class="mt-3 text-3xl font-black text-blue-950 sm:text-4xl">{{ setting('services_title') }}</h2>
                <p class="mt-3 text-sm leading-6 text-blue-950/65">{{ setting('services_description') }}</p>
            </div>

            <div class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($showcase as $application)
                    <article class="group overflow-hidden rounded-2xl border border-sky-100 bg-white shadow-lg shadow-blue-950/5 transition hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-950/10">
                        <div class="grid min-h-64 grid-cols-[1fr_auto]">
                            <div class="p-6">
                                <span class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-full bg-blue-950 text-xl font-black text-white">
                                    @if ($application->thumbnail_url)
                                        <img src="{{ $application->thumbnail_url }}" alt="{{ $application->name }}" class="h-full w-full object-cover">
                                    @else
                                        {{ str($application->short_name ?: $application->name)->substr(0, 2)->upper() }}
                                    @endif
                                </span>
                                <h3 class="mt-5 text-2xl font-black text-blue-950">{{ $application->name }}</h3>
                                <p class="mt-3 line-clamp-3 text-sm leading-6 text-blue-950/70">{{ $application->display_description ?: 'Layanan digital Fakultas Farmasi UBP Karawang.' }}</p>
                                @if ($application->is_linkable)
                                    <a href="{{ route('applications.go', $application) }}" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex items-center gap-3 rounded-full border border-blue-950 px-5 py-2 text-sm font-black text-blue-950 transition hover:bg-blue-950 hover:text-white" aria-label="Buka {{ $application->name }}">
                                        {{ $application->display_button_label }}
                                        <span>-></span>
                                    </a>
                                @else
                                    <span class="mt-5 inline-flex rounded-full bg-slate-100 px-5 py-2 text-sm font-black text-slate-500">{{ $application->status === 'maintenance' ? 'Maintenance' : 'Segera Hadir' }}</span>
                                @endif
                            </div>
                            <div class="hidden w-28 items-end bg-gradient-to-b from-sky-50 to-sky-100 md:flex">
                                <div class="h-32 w-full bg-[linear-gradient(135deg,transparent_35%,rgba(8,43,95,0.12)_35%,rgba(8,43,95,0.12)_55%,transparent_55%)]"></div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-sky-200 bg-white p-8 text-center text-sm text-blue-950/70 md:col-span-2 xl:col-span-3">
                        Belum ada layanan aktif. Tambahkan layanan dari Admin > Aplikasi.
                    </div>
                @endforelse
            </div>
        </section>

        <section id="tentang" class="mx-auto max-w-7xl scroll-mt-24 px-5 pb-12 sm:px-8 lg:px-10">
            <div class="grid gap-8 rounded-3xl bg-blue-950 p-6 text-white shadow-2xl shadow-blue-950/15 lg:grid-cols-[1fr_1.2fr] lg:p-8">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-amber-300">{{ setting('about_eyebrow') }} --</p>
                    <h2 class="mt-3 text-3xl font-black sm:text-4xl">{{ setting('about_title') }}</h2>
                    <p class="mt-4 text-sm leading-7 text-slate-200">{{ setting('about_description') }}</p>
                    <a href="#kontak" class="mt-6 inline-flex items-center gap-3 rounded-full border border-white/80 px-5 py-2.5 text-sm font-black transition hover:bg-white hover:text-blue-950">
                        {{ setting('about_button_label') }}
                        <span>-></span>
                    </a>
                </div>

                @if ($credibleItems->isNotEmpty())
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                        @foreach ($credibleItems as $item)
                            <div class="rounded-2xl border border-amber-300/60 p-4 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border border-amber-300 text-2xl font-black text-amber-300">{{ str($item)->substr(0, 1)->upper() }}</div>
                                <p class="mt-3 text-xs font-black text-amber-200">{{ $item }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <section id="pengumuman" class="mx-auto max-w-7xl scroll-mt-24 px-5 pb-12 sm:px-8 lg:px-10" aria-labelledby="berita-heading">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-black uppercase tracking-wide text-amber-500">-- {{ setting('news_eyebrow') }} --</p>
                <h2 id="berita-heading" class="mt-3 text-3xl font-black text-blue-950 sm:text-4xl">{{ setting('news_title') }}</h2>
            </div>

            @if ($announcements->isNotEmpty())
                <div class="mt-8 grid gap-5 lg:grid-cols-3">
                    @foreach ($announcements as $announcement)
                        <article class="overflow-hidden rounded-2xl border border-sky-100 bg-white shadow-lg shadow-blue-950/5">
                            <div class="h-36 bg-gradient-to-br from-blue-950 via-blue-800 to-sky-500 p-5 text-white">
                                <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-black">{{ str($announcement->type ?: 'info')->headline() }}</span>
                            </div>
                            <div class="p-5">
                                <div class="flex items-center justify-between gap-4 text-xs font-semibold text-slate-500">
                                    <span>Pengumuman</span>
                                    <span>{{ optional($announcement->created_at)->translatedFormat('d M Y') }}</span>
                                </div>
                                <h3 class="mt-3 line-clamp-2 text-lg font-black text-blue-950">{{ $announcement->title }}</h3>
                                @if ($announcement->body)
                                    <p class="mt-3 line-clamp-3 text-sm leading-6 text-blue-950/70">{{ $announcement->body }}</p>
                                @endif
                                @if ($announcement->url)
                                    <a href="{{ $announcement->url }}" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex text-sm font-black text-blue-900">
                                        Baca Selengkapnya ->
                                    </a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="mt-8 rounded-2xl border border-dashed border-sky-200 bg-white p-8 text-center text-sm text-blue-950/70">
                    Belum ada pengumuman aktif.
                </div>
            @endif
        </section>

        <section id="aplikasi" class="border-y border-sky-100 bg-white">
            <div class="mx-auto max-w-7xl px-5 py-12 sm:px-8 lg:px-10">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-wide text-amber-500">Direktori Aplikasi</p>
                        <h2 class="mt-2 text-3xl font-black text-blue-950">Semua layanan SAFA UBP</h2>
                    </div>
                    <p class="max-w-sm text-sm leading-6 text-blue-950/65">Gunakan pencarian dan filter kategori. Data kartu ini dikelola dari admin.</p>
                </div>

                <form action="{{ route('landing') }}#aplikasi" method="GET" class="mt-6 rounded-2xl border border-sky-100 bg-sky-50/70 p-3 shadow-sm">
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
                            class="min-h-12 w-full rounded-xl border border-sky-100 bg-white px-4 text-sm text-blue-950 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                        <button type="submit" class="min-h-12 rounded-xl bg-blue-950 px-6 text-sm font-black text-white transition hover:bg-blue-900">
                            Cari
                        </button>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2" aria-label="Filter kategori">
                        <a href="{{ route('landing', array_filter(['q' => $search])) }}#aplikasi" class="rounded-full px-4 py-2 text-sm font-black transition {{ $selectedCategory === '' ? 'bg-blue-950 text-white shadow-sm' : 'bg-white text-blue-950 ring-1 ring-sky-100 hover:bg-sky-50' }}">
                            Semua
                        </a>
                        @foreach ($categories as $category)
                            <a href="{{ route('landing', array_filter(['q' => $search, 'category' => $category->slug])) }}#aplikasi" class="rounded-full px-4 py-2 text-sm font-black transition {{ $selectedCategory === $category->slug ? 'bg-blue-950 text-white shadow-sm' : 'bg-white text-blue-950 ring-1 ring-sky-100 hover:bg-sky-50' }}">
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
                    <div class="mt-7 rounded-2xl border border-dashed border-sky-200 bg-white p-8 text-center">
                        <h3 class="text-lg font-black text-blue-950">Aplikasi tidak ditemukan.</h3>
                        <p class="mt-2 text-sm text-blue-950/65">Coba gunakan kata kunci lain atau pilih kategori Semua.</p>
                    </div>
                @endif
            </div>
        </section>

        <section id="kontak" class="bg-blue-950">
            <div class="mx-auto max-w-7xl px-5 py-10 sm:px-8 lg:px-10">
                <div class="grid gap-6 text-white md:grid-cols-[1fr_auto] md:items-center">
                    <div>
                        <p class="text-sm font-black uppercase tracking-wide text-amber-300">Kontak</p>
                        <h2 class="mt-2 text-3xl font-black">{{ setting('contact_title') }}</h2>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">{{ setting('contact_description') }}</p>
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row md:justify-end">
                        @if (setting('contact_email'))
                            <a href="mailto:{{ setting('contact_email') }}" class="rounded-full bg-white px-5 py-3 text-sm font-black text-blue-950 transition hover:bg-amber-100">
                                {{ setting('contact_email') }}
                            </a>
                        @endif
                        @if (setting('contact_whatsapp'))
                            <a href="https://wa.me/{{ preg_replace('/\D+/', '', setting('contact_whatsapp')) }}" target="_blank" rel="noopener noreferrer" class="rounded-full bg-white px-5 py-3 text-sm font-black text-blue-950 transition hover:bg-amber-100">
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

    <footer class="border-t border-sky-100 bg-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-2 px-5 py-6 text-sm font-semibold text-blue-950/65 sm:flex-row sm:items-center sm:justify-between sm:px-8 lg:px-10">
            <span>{{ setting('footer_text') }} &copy; {{ now()->year }}</span>
            <span>{{ $siteName }} - {{ $siteSubtitle }}.</span>
        </div>
    </footer>
</body>
</html>
