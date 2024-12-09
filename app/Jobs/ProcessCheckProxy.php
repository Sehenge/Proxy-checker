<?php

namespace App\Jobs;

use App\Models\Proxy;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\NoReturn;

class ProcessCheckProxy implements ShouldQueue
{
    use Queueable;

    private Proxy $proxy;

    /**
     * Create a new job instance.
     */
    #[NoReturn]
    public function __construct(Proxy $proxy)
    {
        $this->proxy = $proxy;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $waitTimeoutInSeconds = 1;
            $bool = false;
            if ($fp = @fsockopen(
                $this->proxy->ip,
                $this->proxy->port,
                $errCode,
                $errStr,
                $waitTimeoutInSeconds
            )) {
                $bool = true;
            }
            Proxy::query()
                ->where('ip', $this->proxy->ip)
                ->update(
                    ['is_active' => $bool]
                );
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        dump($this->proxy->ip.':'.$this->proxy->port.' -> '.($bool ? 'true' : 'false'));
    }
}
