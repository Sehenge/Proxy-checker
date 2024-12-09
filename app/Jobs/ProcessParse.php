<?php

namespace App\Jobs;

use App\Models\Proxy;
use App\Models\Source;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\NoReturn;

class ProcessParse implements ShouldQueue
{
    use Queueable;

    private Source $source;

    /**
     * Create a new job instance.
     */
    #[NoReturn]
    public function __construct(Source $source)
    {
        $this->source = $source;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $proxiesJson = Http::get($this->source->url)->getBody()->getContents();
            $proxiesArray = json_decode($proxiesJson, true);

            foreach ($proxiesArray['data'] as $newProxy) {
                Proxy::query()->updateOrInsert(
                    [
                        'ip' => $newProxy['ip'],
                        'port' => $newProxy['port'],
                        'country' => $newProxy['country'],
                        'protocol' => $newProxy['protocols'][0],
                        'response_time' => $newProxy['responseTime'],
                    ]
                );
                Log::channel('parse')->info(round(memory_get_usage() / 1024 / 1024, 2).' MB');
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        dump('Job parse finished');
    }
}
