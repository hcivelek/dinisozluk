import './bootstrap';

const FAVORITES_KEY = 'ds_favorites';
const THEME_KEY = 'ds_theme';

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
