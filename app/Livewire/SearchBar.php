<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\Word;
use Illuminate\Support\Collection;

class SearchBar extends Component
{
    public $keyword;
    public $activeLetter = 'A';
    #[Url(as: 'kelime', except: '')]
    public $selected = '';
    public $featuredWord;
    public bool $hasSearched = false;
    public string $submittedKeyword = '';

    public function search()
    {
        $keyword = trim((string) $this->keyword);

        if ($keyword === '') {
            $this->loadLetter($this->activeLetter);
            return;
        }

        $this->activeLetter = '';
        $this->selected = '';
        $this->hasSearched = true;
        $this->submittedKeyword = $keyword;
        $this->dispatch('search-completed');
    }

    public function loadLetter(string $letter)
    {
        $this->activeLetter = $letter;
        $this->keyword = '';
        $this->selected = '';
        $this->hasSearched = false;
        $this->submittedKeyword = '';
    }

    public function mount()
    {
        $this->featuredWord = $this->randomFeaturedWord();

        if ($this->selected !== '') {
            $word = Word::where('word', $this->selected)->first();

            if ($word) {
                $this->keyword = $word->word;
                $this->activeLetter = '';
                $this->hasSearched = true;
                $this->submittedKeyword = $word->word;
                return;
            }

            $this->selected = '';
        }

        $this->loadLetter($this->activeLetter);
    }

    public function select(string $word, string $keyword = '')
    {
        $this->selected = $word;
        $keyword = trim($keyword);

        if ($keyword !== '') {
            $this->keyword = $keyword;
            $this->activeLetter = '';
            $this->hasSearched = true;
            $this->submittedKeyword = $keyword;
        }

        $this->dispatch('word-selected', word: $word);
    }

    public function render()
    {
        $result = $this->hasSearched
            ? $this->searchWords($this->submittedKeyword)
            : Word::where('word', 'like', "{$this->activeLetter}%")
                ->orderBy('word')
                ->get();

        return view('livewire.search-bar', [
            'result' => $result,
            'selected' => $this->selected ?? '',
            'activeLetter' => $this->activeLetter,
            'featuredWord' => $this->featuredWord,
            'hasSearched' => $this->hasSearched,
        ]);
    }

    private function searchWords(string $keyword): Collection
    {
        $normalizedKeyword = $this->normalizeText($keyword);
        $words = Word::query()
            ->select('id', 'word', 'search', 'detail')
            ->orderBy('word')
            ->get();

        return $words
            ->map(function (Word $word) use ($normalizedKeyword) {
                $haystack = $this->normalizeText($word->word . ' ' . $word->search);
                $wordOnly = $this->normalizeText($word->word);
                $score = $this->matchScore($normalizedKeyword, $haystack, $wordOnly);

                return ['word' => $word, 'score' => $score];
            })
            ->filter(fn (array $row) => $row['score'] !== null)
            ->sort(fn (array $a, array $b) => $a['score'] <=> $b['score'] ?: strnatcasecmp($a['word']->word, $b['word']->word))
            ->pluck('word')
            ->values();
    }

    private function matchScore(string $needle, string $haystack, string $word): ?int
    {
        if ($needle === '') {
            return null;
        }

        if ($word === $needle) {
            return 0;
        }

        if (str_starts_with($word, $needle)) {
            return 1;
        }

        if (str_contains($haystack, $needle)) {
            return 2;
        }

        $tokens = preg_split('/\s+/', $haystack, -1, PREG_SPLIT_NO_EMPTY);
        $limit = $this->fuzzyLimit($needle);

        foreach ($tokens as $token) {
            if (abs(strlen($token) - strlen($needle)) > $limit) {
                continue;
            }

            $distance = levenshtein($needle, $token);

            if ($distance <= $limit) {
                $prefixPenalty = str_starts_with($token, substr($needle, 0, 2)) ? 0 : 5;
                return 3 + $distance + $prefixPenalty;
            }
        }

        return null;
    }

    private function fuzzyLimit(string $keyword): int
    {
        $length = strlen($keyword);

        if ($length <= 4) {
            return 1;
        }

        return $length <= 8 ? 2 : 3;
    }

    private function randomFeaturedWord(): ?Word
    {
        $count = Word::count();

        if ($count === 0) {
            return null;
        }

        return Word::query()
            ->select('id', 'word', 'search', 'detail')
            ->inRandomOrder()
            ->first();
    }

    private function normalizeText(string $value): string
    {
        $value = mb_strtolower($value, 'UTF-8');
        $value = preg_replace('/\p{Mn}+/u', '', $value) ?? $value;
        $value = strtr($value, [
            'â' => 'a',
            'î' => 'i',
            'û' => 'u',
            'ç' => 'c',
            'ğ' => 'g',
            'ı' => 'i',
            'i' => 'i',
            'ö' => 'o',
            'ş' => 's',
            'ü' => 'u',
        ]);
        $value = preg_replace('/[^a-z0-9\s]+/', ' ', $value) ?? '';

        return trim(preg_replace('/\s+/', ' ', $value) ?? '');
    }
}
