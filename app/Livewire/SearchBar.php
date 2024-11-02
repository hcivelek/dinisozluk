<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Word;

class SearchBar extends Component
{
    public $keyword;
    private $result;
    private $selected;

    public function search(){
        $this->result = Word::where("search", "like", "%{$this->keyword}%")->get();
    }

    public function mount(){
        $this->search();
    }

    public function select(string $word, string $keyword){
        $this->selected = $word;
        $this->search();
    }

    public function render()
    {
        return view('livewire.search-bar',[
            'result' => $this->result ?? [],
            'selected' => $this->selected ?? ''
        ]);
    }
}
