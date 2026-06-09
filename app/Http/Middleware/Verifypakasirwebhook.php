<?php

namespace App\Http\Middleware;

use App\Services\PakasirService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyPakasirWebhook
{
    public function __construct(protected PakasirService $pakasirService)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $signature = $request->header('X-Pakasir-Signature');

        if (empty($signature)) {
            Log::warning('[Pakasir] Webhook diterima tanpa header X-Pakasir-Signature', [
                'ip'   => $request->ip(),
                'path' => $request->path(),
            ]);

            return response()->json([
                'message' => 'Signature tidak ditemukan.',
            ], 403);
        }

        $payload = $request->all();

        $valid = $this->pakasirService->verifyWebhookSignature($payload, $signature);

        if (!$valid) {
            Log::warning('[Pakasir] Webhook signature tidak valid', [
                'ip'           => $request->ip(),
                'merchant_ref' => $payload['merchant_ref'] ?? 'unknown',
                'signature'    => $signature,
            ]);

            return response()->json([
                'message' => 'Signature tidak valid.',
            ], 403);
        }

        return $next($request);
    }
}