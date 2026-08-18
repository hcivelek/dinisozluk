<?php

use App\Models\Word;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/arama-dizini.json', function () {
    $words = Word::query()
        ->select('word', 'search')
        ->orderBy('word')
        ->get();

    return response()
        ->json($words)
        ->header('Cache-Control', 'public, max-age=3600, stale-while-revalidate=86400');
})->name('search.index');

Route::get('/takvim', function(){
    return view('takvim');
});
