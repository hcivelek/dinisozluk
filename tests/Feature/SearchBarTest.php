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
            ->call('search')
            ->assertSet('hasSearched', true)
            ->assertSee('ÂHİRET');
    }

    public function test_search_index_contains_typo_tolerant_candidates(): void
    {
        $this->getJson(route('search.index'))
            ->assertOk()
            ->assertHeader('Cache-Control', 'max-age=3600, public, stale-while-revalidate=86400')
            ->assertJsonFragment(['word' => 'ÂHİRET'])
            ->assertJsonFragment(['word' => 'Âhiret Âlimi']);
    }

    public function test_search_input_remains_visible_with_results(): void
    {
        Livewire::test(SearchBar::class)
            ->set('keyword', 'Ahiret')
            ->call('search')
            ->assertSeeHtml('id="search-input"')
            ->assertSee('ÂHİRET');
    }

    public function test_empty_search_result_has_guidance(): void
    {
        Livewire::test(SearchBar::class)
            ->set('keyword', 'bulunmayankelimexyz')
            ->call('search')
            ->assertSee('Kelime bulunamadı');
    }
}
