<?php

namespace Tests\Feature;

use App\Livewire\SearchBar;
use Livewire\Livewire;
use Tests\TestCase;

class SearchBarTest extends TestCase
{
    public function test_search_tolerates_common_ahiret_misspelling(): void
    {
        Livewire::test(SearchBar::class)
            ->set('keyword', 'Ahret')
            ->assertSee('ÂHİRET');
    }

    public function test_search_shows_live_suggestions(): void
    {
        Livewire::test(SearchBar::class)
            ->set('keyword', 'Ahret')
            ->assertSee('Âhiret Âlimi');
    }
}
