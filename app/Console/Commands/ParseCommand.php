<?php

namespace App\Console\Commands;

use App\Jobs\ProcessParse;
use App\Models\Source;
use Illuminate\Console\Command;

class ParseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:parse';

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
        dump('Parse started');

        $sources = Source::query()->get();

        foreach ($sources as $source) {
            dispatch_sync(new ProcessParse($source));
        }

        dump('Parse finished');
    }
}
