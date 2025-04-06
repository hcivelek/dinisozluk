<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Day;

class Calendar extends Component
{   
    public $keyword;
    public $kameri_yil;
    public $miladi_yil;
    private $result;
    private $selected;
    private $years;

    public function __construct()
    {
        $this->miladi_yil = date("Y");
        $this->search();
    }

    public function search(){
        $query = Day::select("kameri_gun","kameri_yil","miladi_yil","mubarek_gun","gun","miladi");

        if(isset($this->kameri_yil))
        {
            $query->where('kameri_yil', $this->kameri_yil);
        }

        if(isset($this->miladi_yil)){
            $query->where('miladi_yil', $this->miladi_yil);
        }
        
        $this->result = $query->get();
    }

    public function select(string $word, string $keyword){
        $this->selected = $word;
        $this->search();
    }
    
    public function render()
    {
        return view('livewire.calendar',[
            'result' => $this->result ?? [],
            'selected' => $this->selected ?? ''
        ]);
        
    }
}
