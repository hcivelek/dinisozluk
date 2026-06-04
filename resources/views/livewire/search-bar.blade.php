<div class="min-h-screen overflow-x-hidden">
    @php
        $letters = ['A','B','C','Ç','D','E','F','G','Ğ','H','I','İ','J','K','L','M','N','O','Ö','P','R','S','Ş','T','U','Ü','V','Y','Z'];
        $groupedResults = collect($result)->groupBy(fn ($row) => mb_strtoupper(mb_substr($row->word, 0, 1, 'UTF-8'), 'UTF-8'));
        $resultCount = collect($result)->count();
        $featuredWord = collect($result)->first();
    @endphp

    <header class="site-header sticky top-0 z-50">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6">
            <a href="/" class="group flex shrink-0 items-center gap-3" wire:navigate>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-olive-500 text-white shadow-md transition-colors group-hover:bg-olive-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25A8.966 8.966 0 0 1 18 3.75c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.966 8.966 0 0 0-6 2.292m0-14.25v14.25"/>
                    </svg>
                </span>
                <span class="font-serif text-lg font-semibold tracking-tight text-olive-600">Dini Sözlük</span>
            </a>

            <div class="flex items-center gap-2 text-sm text-slate-500">
                <span class="hidden items-center gap-1.5 lg:flex">
                    <kbd class="rounded border border-slate-200 bg-white px-1.5 py-0.5 font-mono text-xs">Enter</kbd>
                    ara
                </span>
                <button type="button" id="theme-toggle" class="icon-button" aria-label="Karanlık/Aydınlık mod">
                    <svg id="moon-icon" xmlns="http://www.w3.org/2000/svg" class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                    <svg id="sun-icon" xmlns="http://www.w3.org/2000/svg" class="hidden h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364-.707-.707M6.343 6.343l-.707-.707m12.728 0-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 1 1-8 0 4 4 0 0 1 8 0z"/>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <main>
        <section class="hero-section relative overflow-hidden px-4 pb-8 pt-10 text-center sm:px-6 sm:pb-10 sm:pt-16">
            <div class="hero-ornament absolute right-8 top-4 hidden select-none lg:block" aria-hidden="true">☽</div>
            <div class="hero-ornament absolute bottom-3 left-10 hidden select-none text-6xl lg:block" aria-hidden="true">✦</div>

            <div class="mx-auto max-w-3xl">
                <p class="mb-3 text-xs font-semibold uppercase tracking-[0.28em] text-olive-500">İslami Terimler Ansiklopedisi</p>
                <h1 class="font-serif text-4xl font-bold leading-tight text-slate-800 sm:text-5xl lg:text-6xl">
                    Dini <span class="text-olive-500">Sözlük</span>
                </h1>
                <p class="mx-auto mt-4 max-w-xl text-base leading-relaxed text-slate-500 sm:text-lg">
                    İslami terimleri anlamak için sade, modern ve güvenilir bir kaynak.
                </p>

                <form wire:submit="search" class="mx-auto mt-8 max-w-xl">
                    <div class="search-shell relative">
                        <div class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"/>
                            </svg>
                        </div>
                        <input
                            id="search-input"
                            type="search"
                            wire:model="keyword"
                            placeholder="Bir kelime arayın... (örn. Ahiret, Dua)"
                            autocomplete="off"
                            aria-label="Kelime arayın"
                            class="w-full rounded-2xl py-4 pl-12 pr-28 text-base shadow-lg"
                        >
                        <button type="submit" class="search-button absolute inset-y-2 right-2 rounded-xl px-5 text-sm font-semibold text-white">
                            Ara
                        </button>
                    </div>
                </form>

                <div class="mt-5 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-sm text-slate-400">
                    <span class="font-medium text-olive-500">{{ $resultCount }} sonuç</span>
                    <span aria-hidden="true">·</span>
                    <span>Ücretsiz ve açık erişim</span>
                    <span aria-hidden="true">·</span>
                    <a href="/takvim" class="text-olive-500 transition hover:text-olive-700" wire:navigate>Takvim</a>
                </div>
            </div>
        </section>

        @if($featuredWord)
            <section class="mx-auto mb-8 max-w-3xl px-4 sm:px-6">
                <article class="featured-card relative overflow-hidden rounded-[1.5rem] p-6 text-white shadow-2xl sm:p-8">
                    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                        <div class="absolute right-8 top-4 font-serif text-5xl text-white/10">✦</div>
                        <div class="absolute bottom-6 left-6 font-serif text-3xl text-white/10">☽</div>
                        <div class="absolute right-16 top-1/2 font-serif text-8xl text-white/5">✦</div>
                    </div>
                    <div class="relative z-10">
                        <div class="mb-4 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-white/20">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 0 0 .95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 0 0-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 0 0-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 0 0-.363-1.118L1.577 10.1c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 0 0 .951-.69l1.519-4.674z"/>
                                </svg>
                            </span>
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-white/70">Öne çıkan kelime</p>
                        </div>
                        <h2 class="font-serif text-3xl font-bold tracking-tight sm:text-4xl">{{ $featuredWord->word }}</h2>
                        <p class="mt-3 max-w-2xl text-base leading-relaxed text-white/85 sm:text-lg">{{ Str::limit(strip_tags($featuredWord->detail), 190) }}</p>
                    </div>
                </article>
            </section>
        @endif

        <nav class="alpha-nav sticky top-16 z-40 border-b py-2" aria-label="Harf navigasyonu">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <div class="scrollbar-hide flex gap-1 overflow-x-auto py-1">
                    @foreach($letters as $letter)
                        <button
                            type="button"
                            wire:click="loadLetter('{{ $letter }}')"
                            class="alpha-btn {{ $activeLetter === $letter ? 'active-letter' : '' }}"
                            aria-pressed="{{ $activeLetter === $letter ? 'true' : 'false' }}"
                        >{{ $letter }}</button>
                    @endforeach
                </div>
            </div>
        </nav>

        <section class="mx-auto max-w-6xl px-4 py-8 sm:px-6" aria-live="polite">
            @if($resultCount === 0)
                <div class="empty-state py-20 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto mb-6 h-20 w-20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"/>
                    </svg>
                    <h2 class="font-serif text-2xl font-semibold text-slate-700">Kelime bulunamadı</h2>
                    <p class="mx-auto mt-3 max-w-sm text-sm leading-relaxed text-slate-400">
                        Aradığınız kelime henüz sözlüğümüzde bulunmuyor. Yazım hatası olabilir mi?
                    </p>
                </div>
            @else
                <div class="space-y-10">
                    @foreach($groupedResults as $letter => $rows)
                        <section id="letter-{{ $letter }}" class="scroll-mt-28">
                            <h2 class="section-letter mb-4 inline-flex pb-1 text-3xl font-bold">{{ $letter }}</h2>
                            <div class="grid grid-cols-1 items-start gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach($rows as $row)
                                    <article class="word-card rounded-2xl p-5">
                                        <button
                                            type="button"
                                            class="flex w-full items-start justify-between gap-4 text-left"
                                            wire:click="select('{{ addslashes($row->word) }}', '{{ addslashes($keyword ?? '') }}')"
                                        >
                                            <span class="font-serif text-2xl font-semibold text-slate-800">{{ $row->word }}</span>
                                            <span class="card-action mt-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/>
                                                </svg>
                                            </span>
                                        </button>

                                        @if($selected == $row->word)
                                            <div class="definition mt-4 border-t border-slate-100 pt-4 text-sm leading-7 text-slate-600">
                                                {!! nl2br($row->detail) !!}
                                            </div>

                                            <div class="mt-4 flex justify-end border-t border-slate-100 pt-3">
                                                <a
                                                    href="{{ url('/').'?kelime='.urlencode($row->word) }}"
                                                    class="word-link"
                                                    title="{{ $row->word }} bağlantısını aç"
                                                    aria-label="{{ $row->word }} bağlantısını aç"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        @else
                                            <p class="mt-3 text-sm leading-6 text-slate-500">
                                                {{ Str::limit(strip_tags($row->detail), 135) }}
                                            </p>
                                        @endif
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>
            @endif
        </section>
    </main>

    <footer class="site-footer mt-12 border-t py-8">
        <div class="mx-auto max-w-6xl px-4 text-center text-sm text-slate-400 sm:px-6">
            <p>
                Bu site bir
                <a href="https://www.huzurpinari.com" target="_blank" rel="noopener noreferrer" class="font-medium text-olive-500 hover:underline">Huzur Pınarı</a>
                hizmetidir.
            </p>
            <p class="mt-2 text-xs text-slate-300">© {{ date('Y') }} Dini Sözlük</p>
        </div>
    </footer>

    <button type="button" onclick="scrollToTop()" id="backToTopBtn" class="back-to-top" aria-label="Yukarı çık">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="m5 15 7-7 7 7"/>
        </svg>
    </button>

    <script>
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        document.addEventListener('scroll', () => {
            const backToTopBtn = document.getElementById('backToTopBtn');
            if (!backToTopBtn) return;
            backToTopBtn.classList.toggle('visible', window.scrollY > 220);
        });

        document.addEventListener('DOMContentLoaded', () => {
            const html = document.documentElement;
            const toggle = document.getElementById('theme-toggle');
            const moon = document.getElementById('moon-icon');
            const sun = document.getElementById('sun-icon');

            const applyTheme = (theme) => {
                html.classList.toggle('dark', theme === 'dark');
                moon?.classList.toggle('hidden', theme === 'dark');
                sun?.classList.toggle('hidden', theme !== 'dark');
            };

            const savedTheme = localStorage.getItem('ds_theme');
            const preferredTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            applyTheme(savedTheme || preferredTheme);

            toggle?.addEventListener('click', () => {
                const nextTheme = html.classList.contains('dark') ? 'light' : 'dark';
                localStorage.setItem('ds_theme', nextTheme);
                applyTheme(nextTheme);
            });
        });
    </script>
</div>
