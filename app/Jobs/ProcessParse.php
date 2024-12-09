<?php

namespace App\Jobs;

use App\Models\Proxy;
use App\Models\Source;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\NoReturn;
use Telegram\Bot\Laravel\Facades\Telegram;

class ProcessParse implements ShouldQueue
{
    use Queueable;

    private Source $dataSource;

    #[NoReturn]
    public function __construct(Source $source)
    {
        $this->dataSource = $source;
    }

    public function handle(): void
    {
        $proxyData = $this->fetchDataFromSource();

        if ($proxyData) {
            $this->processAndPersistData($proxyData);
        }

        $this->sendCompletionReportToTelegram();
    }

    private function fetchDataFromSource()
    {
        try {
            $dataJson = Http::get($this->dataSource->url)->getBody()->getContents();

            return json_decode($dataJson, true);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return false;
        }
    }

    private function processAndPersistData(array $proxyData): void
    {
        foreach ($proxyData['data'] as $proxy) {
            Proxy::query()->updateOrInsert(
                [
                    'ip' => $proxy['ip'],
                    'port' => $proxy['port'],
                    'country' => $proxy['country'],
                    'protocol' => $proxy['protocols'][0],
                    'response_time' => $proxy['responseTime'],
                ]
            );

            Log::channel('parse')->info(round(memory_get_usage() / 1024 / 1024, 2).' MB');
        }
    }

    private function sendCompletionReportToTelegram(): void
    {
        Telegram::sendMessage([
            'chat_id' => config('telegram.bots.telegram_bot.chat_id'),
            'text' => 'Job: Proxy parsing finished successfully!',
        ]);

        dump('Report sent to Telegram');
    }
}
