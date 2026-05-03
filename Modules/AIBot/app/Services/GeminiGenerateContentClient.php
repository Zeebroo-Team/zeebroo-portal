<?php

namespace Modules\AIBot\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiGenerateContentClient
{
    public function generate(array $body): Response
    {
        $apiKey = (string) config('aibot.gemini.api_key', '');
        if ($apiKey === '') {
            throw new RuntimeException('GEMINI_API_KEY is not configured.');
        }

        $model = trim((string) config('aibot.gemini.model', 'gemini-2.0-flash'));
        $timeout = (int) config('aibot.gemini.timeout', 60);
        $url = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent',
            $model
        );

        return Http::timeout($timeout)
            ->retry(2, 500, null, false)
            ->acceptJson()
            ->withQueryParameters(['key' => $apiKey])
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($url, $body);
    }
}
