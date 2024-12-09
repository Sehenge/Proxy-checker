<?php

namespace App\Console\Commands;

use App\Jobs\ProcessCheckProxy;
use App\Models\Proxy;
use Illuminate\Console\Command;

class CheckProxyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-proxy';

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
        $proxies = Proxy::query()
            ->where('response_time', '<', 100)
            ->get();

        foreach ($proxies as $proxy) {
            dispatch_sync(new ProcessCheckProxy($proxy));
        }
    }
}
