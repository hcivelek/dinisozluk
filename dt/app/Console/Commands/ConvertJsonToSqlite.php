<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Word;

class ConvertJsonToSqlite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:convert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $content = file_get_contents("./database/dts.json");
        $json = json_decode($content);
        $rows = [];
        foreach($json as $j){
            
            $data[]=[
                'word' => $j->kelime,
                'search' => $j->arama,
                'detail' => $j->metin
            ];
        }

        Word::insert($data);
    }
}
