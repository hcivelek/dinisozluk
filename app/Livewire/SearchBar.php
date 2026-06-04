<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\Word;

class SearchBar extends Component
{
    public $keyword;
    public $activeLetter = 'A';
    #[Url(as: 'kelime', except: '')]
    public $selected = '';
    private $result;

    public function search(){
        $keyword = trim((string) $this->keyword);

        if ($keyword === '') {
            $this->loadLetter($this->activeLetter);
            return;
        }

        $this->activeLetter = '';
        $this->selected = '';
        $this->result = Word::where("search", "like", "%{$keyword}%")
            ->orderBy('word')
            ->get();
    }

    public function loadLetter(string $letter){
        $this->activeLetter = $letter;
        $this->keyword = '';
        $this->selected = '';
        $this->result = Word::where("word", "like", "{$letter}%")
            ->orderBy('word')
            ->get();
    }

    public function mount(){
        if ($this->selected !== '') {
            $word = Word::where('word', $this->selected)->first();

            if ($word) {
                $this->activeLetter = mb_strtoupper(mb_substr($word->word, 0, 1, 'UTF-8'), 'UTF-8');
                $this->result = Word::where("word", "like", "{$this->activeLetter}%")
                    ->orderBy('word')
                    ->get();
                return;
            }

            $this->selected = '';
        }

        $this->loadLetter($this->activeLetter);
    }

    public function select(string $word, string $keyword){
        $this->selected = $word;

        if (trim($keyword) !== '') {
            $this->result = Word::where("search", "like", "%{$keyword}%")
                ->orderBy('word')
                ->get();
            return;
        }

        $this->result = Word::where("word", "like", "{$this->activeLetter}%")
            ->orderBy('word')
            ->get();
    }

    public function render()
    {
        return view('livewire.search-bar',[
            'result' => $this->result ?? [],
            'selected' => $this->selected ?? '',
            'activeLetter' => $this->activeLetter,
        ]);
    }
}
