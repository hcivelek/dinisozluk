import './bootstrap';

const FAVORITES_KEY = 'ds_favorites';
const THEME_KEY = 'ds_theme';
let searchIndexPromise;

function normalizeSearchText(value) {
    const replacements = { ç: 'c', ğ: 'g', ı: 'i', ö: 'o', ş: 's', ü: 'u' };

    return value
        .toLocaleLowerCase('tr-TR')
        .normalize('NFD')
        .replace(/\p{M}+/gu, '')
        .replace(/[çğıöşü]/g, (character) => replacements[character])
        .replace(/[^a-z0-9\s]+/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
}

function levenshteinDistance(left, right) {
    if (left === right) return 0;
    if (left.length === 0) return right.length;
    if (right.length === 0) return left.length;

    let previous = Array.from({ length: right.length + 1 }, (_, index) => index);

    for (let leftIndex = 1; leftIndex <= left.length; leftIndex += 1) {
        const current = [leftIndex];

        for (let rightIndex = 1; rightIndex <= right.length; rightIndex += 1) {
            const substitution = previous[rightIndex - 1] + (left[leftIndex - 1] === right[rightIndex - 1] ? 0 : 1);
            current[rightIndex] = Math.min(
                current[rightIndex - 1] + 1,
                previous[rightIndex] + 1,
                substitution,
            );
        }

        previous = current;
    }

    return previous[right.length];
}

function fuzzyLimit(keyword) {
    if (keyword.length <= 4) return 1;
    return keyword.length <= 8 ? 2 : 3;
}

function suggestionScore(needle, entry) {
    if (entry.wordOnly === needle) return 0;
    if (entry.wordOnly.startsWith(needle)) return 1;
    if (entry.haystack.includes(needle)) return 2;

    const limit = fuzzyLimit(needle);

    for (const token of entry.tokens) {
        if (Math.abs(token.length - needle.length) > limit) continue;

        const distance = levenshteinDistance(needle, token);
        if (distance <= limit) {
            return 3 + distance + (token.startsWith(needle.slice(0, 2)) ? 0 : 5);
        }
    }

    return null;
}

function loadSearchIndex(url) {
    if (!searchIndexPromise) {
        searchIndexPromise = fetch(url, { headers: { Accept: 'application/json' } })
            .then((response) => {
                if (!response.ok) throw new Error(`Search index request failed: ${response.status}`);
                return response.json();
            })
            .then((words) => words.map((entry) => {
                const wordOnly = normalizeSearchText(entry.word);
                const haystack = normalizeSearchText(`${entry.word} ${entry.search || ''}`);

                return {
                    word: entry.word,
                    wordOnly,
                    haystack,
                    tokens: haystack.split(' '),
                };
            }))
            .catch((error) => {
                searchIndexPromise = undefined;
                throw error;
            });
    }

    return searchIndexPromise;
}

function findSuggestions(index, value) {
    const needle = normalizeSearchText(value);
    if (needle.length < 2) return [];

    return index
        .map((entry) => ({ entry, score: suggestionScore(needle, entry) }))
        .filter((result) => result.score !== null)
        .sort((left, right) => left.score - right.score || left.entry.word.localeCompare(right.entry.word, 'tr'))
        .slice(0, 8)
        .map((result) => result.entry.word);
}

function hideAutocomplete(input, panel) {
    panel.classList.add('hidden');
    panel.replaceChildren();
    input.setAttribute('aria-expanded', 'false');
}

function submitSuggestion(input, panel, word) {
    input.value = word;
    input.dispatchEvent(new Event('input', { bubbles: true }));
    input.dispatchEvent(new Event('change', { bubbles: true }));
    hideAutocomplete(input, panel);
    input.form?.requestSubmit();
}

function renderAutocomplete(input, panel, suggestions) {
    panel.replaceChildren();

    suggestions.forEach((word) => {
        const button = document.createElement('button');
        const label = document.createElement('span');
        const action = document.createElement('span');

        button.type = 'button';
        button.className = 'autocomplete-item flex w-full items-center justify-between px-4 py-3 text-sm';
        button.dataset.suggestion = word;
        button.setAttribute('role', 'option');
        label.textContent = word;
        action.textContent = 'Ara';
        action.setAttribute('aria-hidden', 'true');
        button.append(label, action);
        panel.append(button);
    });

    panel.classList.toggle('hidden', suggestions.length === 0);
    input.setAttribute('aria-expanded', suggestions.length > 0 ? 'true' : 'false');
}

function initializeAutocomplete() {
    const input = document.getElementById('search-input');
    const panel = document.getElementById('autocomplete-panel');
    if (!input || !panel || input.dataset.autocompleteReady === 'true') return;

    input.dataset.autocompleteReady = 'true';
    let timer;

    const preload = () => loadSearchIndex(input.dataset.suggestionsUrl).catch(() => {});
    if ('requestIdleCallback' in window) {
        window.requestIdleCallback(preload, { timeout: 1500 });
    } else {
        window.setTimeout(preload, 250);
    }

    input.addEventListener('input', () => {
        window.clearTimeout(timer);
        const value = input.value;

        if (normalizeSearchText(value).length < 2) {
            hideAutocomplete(input, panel);
            return;
        }

        timer = window.setTimeout(async () => {
            try {
                const index = await loadSearchIndex(input.dataset.suggestionsUrl);
                if (input.value !== value) return;
                renderAutocomplete(input, panel, findSuggestions(index, value));
            } catch {
                hideAutocomplete(input, panel);
            }
        }, 60);
    });

    input.addEventListener('keydown', (event) => {
        const options = [...panel.querySelectorAll('[data-suggestion]')];
        if (event.key === 'Escape') {
            hideAutocomplete(input, panel);
            return;
        }

        if (!['ArrowDown', 'ArrowUp', 'Enter'].includes(event.key) || options.length === 0) return;
        const activeIndex = options.findIndex((option) => option.classList.contains('active'));

        if (event.key === 'Enter' && activeIndex >= 0) {
            event.preventDefault();
            submitSuggestion(input, panel, options[activeIndex].dataset.suggestion);
            return;
        }

        if (event.key === 'Enter') return;
        event.preventDefault();
        options.forEach((option) => option.classList.remove('active'));
        const nextIndex = event.key === 'ArrowDown'
            ? (activeIndex + 1) % options.length
            : (activeIndex <= 0 ? options.length - 1 : activeIndex - 1);
        options[nextIndex].classList.add('active');
        options[nextIndex].scrollIntoView({ block: 'nearest' });
    });

    panel.addEventListener('click', (event) => {
        if (!(event.target instanceof Element)) return;
        const option = event.target.closest('[data-suggestion]');
        if (option) submitSuggestion(input, panel, option.dataset.suggestion);
    });

    input.form?.addEventListener('submit', () => hideAutocomplete(input, panel));
}

function getFavorites() {
    try {
        const value = JSON.parse(localStorage.getItem(FAVORITES_KEY) || '[]');
        return Array.isArray(value) ? value.filter((item) => typeof item === 'string') : [];
    } catch {
        return [];
    }
}

function saveFavorites(items) {
    localStorage.setItem(FAVORITES_KEY, JSON.stringify(items));
}

function wordUrl(word) {
    const url = new URL('/', window.location.origin);
    url.searchParams.set('kelime', word);
    return url.toString();
}

function applyTheme(theme) {
    const isDark = theme === 'dark';
    document.documentElement.classList.toggle('dark', isDark);
    document.getElementById('moon-icon')?.classList.toggle('hidden', isDark);
    document.getElementById('sun-icon')?.classList.toggle('hidden', !isDark);
}

function preferredTheme() {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

function renderFavorites() {
    const items = getFavorites();
    const count = document.getElementById('favorites-count');
    const list = document.getElementById('favorites-list');

    document.querySelectorAll('[data-favorite-word]').forEach((button) => {
        const active = items.includes(button.dataset.favoriteWord);
        button.classList.toggle('active', active);
        button.setAttribute('aria-pressed', active ? 'true' : 'false');
        button.setAttribute('title', active ? 'Favorilerden çıkar' : 'Favorilere ekle');
    });

    if (count) {
        count.textContent = String(items.length);
        count.classList.toggle('hidden', items.length === 0);
    }

    if (!list) return;
    list.replaceChildren();

    if (items.length === 0) {
        const empty = document.createElement('p');
        empty.className = 'px-3 py-6 text-center text-sm text-slate-400';
        empty.textContent = 'Henüz favori eklenmedi.';
        list.append(empty);
        return;
    }

    items.forEach((word) => {
        const link = document.createElement('a');
        link.className = 'favorite-list-item';
        link.href = wordUrl(word);
        link.textContent = word;
        list.append(link);
    });
}

function setFavoritesPanel(open) {
    document.getElementById('favorites-panel')?.classList.toggle('hidden', !open);
    document.getElementById('favorites-toggle')?.setAttribute('aria-expanded', open ? 'true' : 'false');
}

function initializeUi() {
    const savedTheme = localStorage.getItem(THEME_KEY);
    applyTheme(savedTheme || preferredTheme());
    renderFavorites();
    initializeAutocomplete();
}

document.addEventListener('DOMContentLoaded', initializeUi);
document.addEventListener('livewire:navigated', initializeUi);

document.addEventListener('livewire:init', () => {
    window.Livewire.hook('morph.updated', () => {
        applyTheme(localStorage.getItem(THEME_KEY) || preferredTheme());
        renderFavorites();
    });
});

document.addEventListener('scroll', () => {
    document.getElementById('backToTopBtn')?.classList.toggle('visible', window.scrollY > 220);
});

document.addEventListener('click', async (event) => {
    if (!(event.target instanceof Element)) return;

    if (event.target.closest('#backToTopBtn')) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        return;
    }

    if (event.target.closest('#theme-toggle')) {
        const nextTheme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
        localStorage.setItem(THEME_KEY, nextTheme);
        applyTheme(nextTheme);
        return;
    }

    if (event.target.closest('#favorites-toggle')) {
        const panel = document.getElementById('favorites-panel');
        setFavoritesPanel(panel?.classList.contains('hidden') ?? true);
        return;
    }

    if (event.target.closest('[data-close-favorites]')) {
        setFavoritesPanel(false);
        return;
    }

    const favoriteButton = event.target.closest('[data-favorite-word]');
    if (favoriteButton) {
        const word = favoriteButton.dataset.favoriteWord;
        const items = getFavorites();
        saveFavorites(items.includes(word) ? items.filter((item) => item !== word) : [...items, word]);
        renderFavorites();
        return;
    }

    const copyButton = event.target.closest('[data-copy-text]');
    if (copyButton) {
        await navigator.clipboard?.writeText(copyButton.dataset.copyText);
        return;
    }

    const shareButton = event.target.closest('[data-share-word]');
    if (shareButton) {
        const shareData = {
            title: `${shareButton.dataset.shareWord} - Dini Sözlük`,
            text: `${shareButton.dataset.shareWord} - ${shareButton.dataset.shareText}`,
            url: wordUrl(shareButton.dataset.shareWord),
        };

        try {
            if (navigator.share) {
                await navigator.share(shareData);
            } else {
                await navigator.clipboard?.writeText(`${shareData.text} ${shareData.url}`);
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                await navigator.clipboard?.writeText(`${shareData.text} ${shareData.url}`);
            }
        }
    }
});

window.addEventListener('word-selected', (event) => {
    window.setTimeout(() => {
        const target = [...document.querySelectorAll('[data-word-name]')]
            .find((item) => item.dataset.wordName === event.detail.word);
        target?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 120);
});

window.addEventListener('search-completed', () => {
    window.setTimeout(() => {
        document.getElementById('search-results')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 120);
});
