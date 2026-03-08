<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class TelegramSetupController extends Controller
{
    /**
     * Register Telegram Webhook.
     * Akses: GET /telegram/set-webhook?url=https://xxxx.ngrok-free.app
     */
    public function setWebhook(): JsonResponse
    {
        $token      = env('TELEGRAM_BOT_TOKEN');
        $webhookUrl = request('url');

        if (! $webhookUrl) {
            return response()->json([
                'error' => 'Parameter ?url= wajib diisi. Contoh: /telegram/set-webhook?url=https://abc123.ngrok-free.app',
            ], 422);
        }

        $fullUrl  = rtrim($webhookUrl, '/') . '/telegram/webhook';
        $response = Http::post("https://api.telegram.org/bot{$token}/setWebhook", [
            'url' => $fullUrl,
        ]);

        return response()->json([
            'webhook_url'      => $fullUrl,
            'telegram_response' => $response->json(),
        ]);
    }

    /**
     * Remove Telegram Webhook.
     * Akses: GET /telegram/remove-webhook
     */
    public function removeWebhook(): JsonResponse
    {
        $token    = env('TELEGRAM_BOT_TOKEN');
        $response = Http::post("https://api.telegram.org/bot{$token}/deleteWebhook");

        return response()->json([
            'message'           => 'Webhook removed.',
            'telegram_response' => $response->json(),
        ]);
    }

    /**
     * Cek info Webhook saat ini.
     * Akses: GET /telegram/webhook-info
     */
    public function webhookInfo(): JsonResponse
    {
        $token    = env('TELEGRAM_BOT_TOKEN');
        $response = Http::get("https://api.telegram.org/bot{$token}/getWebhookInfo");

        return response()->json($response->json());
    }
}
