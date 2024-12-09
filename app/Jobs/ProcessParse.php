<?php

namespace App\Jobs;

use App\Models\Proxy;
use App\Models\Source;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class ProcessParse implements ShouldQueue
{
    use Queueable;

    private Source $dataSource;

    public function __construct(Source $source)
    {
        $this->dataSource = $source;
    }

    /**
     * Handles the processing of proxy data from a data source and sends completion report to Telegram.
     */
    public function handle(): void
    {
        $proxyData = $this->fetchDataFromSource();

        if ($proxyData) {
            $this->processAndPersistData($proxyData);
        }

        $this->sendCompletionReportToTelegram();
    }

    private function fetchDataFromSource(): array|bool
    {
        try {
            $dataJson = Http::get($this->dataSource->url)->getBody()->getContents();

            return json_decode($dataJson, true);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return false;
        }
    }

    /**
     * Process and persist proxy data in the database.
     *
     * @param  array  $proxyData  Array containing proxy data to process.
     */
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

            //Log::channel('parse')->info(round(memory_get_usage() / 1024 / 1024, 2).' MB');
        }
    }

    /**
     * Send completion report to Telegram chat.
     */
    private function sendCompletionReportToTelegram(): void
    {
        try {
            Telegram::sendMessage([
                'chat_id' => config('telegram.bots.telegram_bot.chat_id'),
                'text' => 'Job: Proxy parsing finished successfully!',
            ]);
            dump('Report sent to Telegram');
        } catch (Exception $e) {
            dump('An error occurred while sending report to Telegram: ', $e->getMessage());
        }
    }
}
