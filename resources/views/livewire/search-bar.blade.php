<div class="min-h-screen overflow-x-hidden">
    @php
        $letters = ['A','B','C','Ç','D','E','F','G','Ğ','H','I','İ','J','K','L','M','N','O','Ö','P','R','S','Ş','T','U','Ü','V','Y','Z'];
        $groupedResults = collect($result)->groupBy(fn ($row) => mb_strtoupper(mb_substr($row->word, 0, 1, 'UTF-8'), 'UTF-8'));
        $resultCount = collect($result)->count();
        $isSearching = $hasSearched ?? false;
        $featuredWord = $featuredWord ?? null;
    @endphp

    <header class="site-header sticky top-0 z-50">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6">
            <a href="/" class="group flex shrink-0 items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-olive-500 text-white shadow-md transition-colors group-hover:bg-olive-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25A8.966 8.966 0 0 1 18 3.75c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.966 8.966 0 0 0-6 2.292m0-14.25v14.25"/>
                    </svg>
                </span>
                <span class="brand-logo-text font-serif text-lg font-semibold tracking-tight"><span>Dini</span> Sözlük</span>
            </a>

            <div class="flex items-center gap-2 text-sm text-slate-500">
                <button type="button" id="favorites-toggle" class="icon-button relative" aria-label="Favorileri göster" aria-expanded="false" aria-controls="favorites-panel">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 0 0 .95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 0 0-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 0 0-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 0 0-.363-1.118L1.577 10.1c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 0 0 .951-.69l1.519-4.674z"/>
                    </svg>
                    <span id="favorites-count" class="favorite-count hidden">0</span>
                </button>
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

    <aside id="favorites-panel" class="favorites-panel hidden" aria-label="Favoriler">
        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
            <h2 class="font-serif text-lg font-semibold text-slate-800">Favoriler</h2>
            <button type="button" data-close-favorites class="icon-button h-8 w-8" aria-label="Favorileri kapat">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></svg>
            </button>
        </div>
        <div id="favorites-list" class="max-h-[min(60vh,28rem)] overflow-y-auto p-2"></div>
    </aside>

    <main>
        <section class="{{ $isSearching ? 'search-results-header' : 'hero-section' }} relative overflow-visible px-4 text-center sm:px-6 {{ $isSearching ? 'py-5 sm:py-6' : 'pb-8 pt-10 sm:pb-10 sm:pt-16' }}">
            @unless($isSearching)
            <div class="hero-ornament absolute right-8 top-4 hidden select-none lg:block" aria-hidden="true">☽</div>
            <div class="hero-ornament absolute bottom-3 left-10 hidden select-none text-6xl lg:block" aria-hidden="true">✦</div>
            @endunless

            <div class="mx-auto max-w-3xl">
                @unless($isSearching)
                <p class="mb-3 text-xs font-semibold uppercase tracking-[0.28em] text-olive-500">İslami Terimler Ansiklopedisi</p>
                <h1 class="font-serif text-4xl font-bold leading-tight text-slate-800 sm:text-5xl lg:text-6xl">
                    Dini <span class="text-olive-500">Sözlük</span>
                </h1>
                <p class="mx-auto mt-4 max-w-xl text-base leading-relaxed text-slate-500 sm:text-lg">
                    İslami terimleri anlamak için sade, modern ve güvenilir bir kaynak.
                </p>
                @endunless

                <form wire:submit="search" class="mx-auto max-w-xl {{ $isSearching ? '' : 'mt-8' }}">
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
                            data-suggestions-url="{{ route('search.index') }}"
                            aria-autocomplete="list"
                            aria-controls="autocomplete-panel"
                            aria-expanded="false"
                            placeholder="Bir kelime arayın... (örn. Ahiret, Dua)"
                            autocomplete="off"
                            aria-label="Kelime arayın"
                            class="w-full rounded-2xl py-4 pl-12 pr-28 text-base shadow-lg"
                        >
                        <button type="submit" class="search-button absolute inset-y-2 right-2 rounded-xl px-5 text-sm font-semibold text-white">
                            Ara
                        </button>
                    </div>
                    <div id="autocomplete-panel" class="autocomplete-panel absolute left-1/2 z-50 mt-2 hidden w-[calc(100%-2rem)] max-w-xl -translate-x-1/2 overflow-hidden rounded-xl text-left shadow-xl sm:w-full" role="listbox"></div>
                </form>

                @unless($isSearching)
                <div class="mt-5 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-sm text-slate-400">
                    <span class="font-medium text-olive-500">{{ $resultCount }} sonuç</span>
                    <span aria-hidden="true">·</span>
                    <a href="https://www.huzurpinari.com" target="_blank" rel="noopener noreferrer" class="text-olive-500 transition hover:text-olive-700">huzurpinari.com</a>
                </div>
                @endunless
            </div>
        </section>

        @if(!$isSearching && $featuredWord)
            <section class="mx-auto mb-8 max-w-3xl px-4 sm:px-6">
                <article class="featured-card relative cursor-pointer overflow-hidden rounded-[1.5rem] p-6 text-white shadow-2xl sm:p-8" role="button" tabindex="0" wire:click="select('{{ addslashes($featuredWord->word) }}', '{{ addslashes($featuredWord->word) }}')">
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

        <section id="search-results" class="mx-auto max-w-6xl scroll-mt-36 px-4 py-8 sm:px-6" aria-live="polite">
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
                                    <article id="word-{{ md5($row->word) }}" data-word-name="{{ $row->word }}" class="word-card rounded-2xl p-5">
                                        <button
                                            type="button"
                                            class="flex w-full items-start justify-between gap-4 text-left"
                                            wire:click="select('{{ addslashes($row->word) }}', '{{ addslashes($submittedKeyword ?? '') }}')"
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

                                            <div class="word-actions mt-4 flex justify-end gap-2 border-t border-slate-100 pt-3">
                                                <button type="button" class="word-link favorite-button" data-favorite-word="{{ $row->word }}" title="Favorilere ekle" aria-label="{{ $row->word }} favorilere ekle">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 0 0 .95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 0 0-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 0 0-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 0 0-.363-1.118L1.577 10.1c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 0 0 .951-.69l1.519-4.674z"/></svg>
                                                </button>
                                                <button type="button" class="word-link" data-copy-text="{{ $row->word }} - {{ Str::limit(strip_tags($row->detail), 240) }}" title="Kopyala" aria-label="{{ $row->word }} kopyala">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                                                </button>
                                                <button type="button" class="word-link" data-share-word="{{ $row->word }}" data-share-text="{{ Str::limit(strip_tags($row->detail), 240) }}" title="Paylaş" aria-label="{{ $row->word }} paylaş">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.59 13.51 6.83 3.98M15.41 6.51 8.59 10.49"/></svg>
                                                </button>
                                                <a href="/?kelime={{ urlencode($row->word) }}" class="word-link" title="{{ $row->word }} bağlantısını aç" aria-label="{{ $row->word }} bağlantısını aç">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                                </a>
                                            </div>
                                        @else
                                            <p class="mt-3 text-sm leading-6 text-slate-500">
                                                {{ Str::limit(strip_tags($row->detail), 135) }}
                                            </p>
                                            @if(Str::length(strip_tags($row->detail)) > 135)
                                                <button
                                                    type="button"
                                                    class="read-more mt-3 text-sm font-semibold"
                                                    wire:click="select('{{ addslashes($row->word) }}', '{{ addslashes($submittedKeyword ?? '') }}')"
                                                >Devamını oku</button>
                                            @endif
                                            <div class="word-actions mt-4 flex justify-end gap-2 border-t border-slate-100 pt-3">
                                                <button type="button" class="word-link favorite-button" data-favorite-word="{{ $row->word }}" title="Favorilere ekle" aria-label="{{ $row->word }} favorilere ekle">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 0 0 .95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 0 0-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 0 0-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 0 0-.363-1.118L1.577 10.1c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 0 0 .951-.69l1.519-4.674z"/></svg>
                                                </button>
                                                <button type="button" class="word-link" data-copy-text="{{ $row->word }} - {{ Str::limit(strip_tags($row->detail), 240) }}" title="Kopyala" aria-label="{{ $row->word }} kopyala">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                                                </button>
                                                <button type="button" class="word-link" data-share-word="{{ $row->word }}" data-share-text="{{ Str::limit(strip_tags($row->detail), 240) }}" title="Paylaş" aria-label="{{ $row->word }} paylaş">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.59 13.51 6.83 3.98M15.41 6.51 8.59 10.49"/></svg>
                                                </button>
                                            </div>
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

    <button type="button" id="backToTopBtn" class="back-to-top" aria-label="Yukarı çık">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="m5 15 7-7 7 7"/>
        </svg>
    </button>

</div>
