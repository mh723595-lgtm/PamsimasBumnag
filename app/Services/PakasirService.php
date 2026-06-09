<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PakasirService
{
    protected string $merchantSlug;
    protected string $apiKey;
    protected string $baseUrl;
    protected string $callbackUrl;
    protected string $successUrl;
    protected string $failedUrl;
    protected int $expiryMinutes;

    public function __construct()
    {
        $this->merchantSlug  = config('pakasir.merchant_slug');
        $this->apiKey        = config('pakasir.api_key');
        $this->baseUrl       = rtrim(config('pakasir.base_url'), '/');
        $this->callbackUrl   = config('pakasir.callback_url');
        $this->successUrl    = config('pakasir.success_url');
        $this->failedUrl     = config('pakasir.failed_url');
        $this->expiryMinutes = (int) config('pakasir.expiry_minutes', 60);
    }

    /**
     * Buat transaksi baru di Pakasir.
     *
     * @param array $payload
     *   Wajib: amount (int), merchant_ref (string), customer_name (string)
     *   Opsional: customer_phone, description
     * @return array ['success'=>bool, 'data'=>array, 'message'=>string]
     */
    public function createTransaction(array $payload): array
    {
        $expiredAt = now()->addMinutes($this->expiryMinutes)->toIso8601String();

        $body = [
            'merchant_slug' => $this->merchantSlug,
            'amount'        => (int) $payload['amount'],
            'merchant_ref'  => $payload['merchant_ref'],
            'customer_name' => $payload['customer_name'],
            'customer_phone'=> $payload['customer_phone'] ?? '',
            'description'   => $payload['description']    ?? 'Pembayaran tagihan air PAMSIMAS',
            'callback_url'  => $this->callbackUrl,
            'success_url'   => $payload['success_url']    ?? $this->successUrl . '?ref=' . $payload['merchant_ref'],
            'failed_url'    => $payload['failed_url']     ?? $this->failedUrl  . '?ref=' . $payload['merchant_ref'],
            'expired_at'    => $expiredAt,
        ];

        Log::info('[Pakasir] createTransaction request', [
            'merchant_ref' => $body['merchant_ref'],
            'amount'       => $body['amount'],
            'customer'     => $body['customer_name'],
        ]);

        try {
            $response = Http::withHeaders([
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization'=> 'Bearer ' . $this->apiKey,
            ])->timeout(30)->post($this->baseUrl . '/transactions', $body);

            $responseData = $response->json();

            Log::info('[Pakasir] createTransaction response', [
                'status'       => $response->status(),
                'merchant_ref' => $body['merchant_ref'],
                'body'         => $responseData,
            ]);

            if ($response->successful() && isset($responseData['payment_url'])) {
                return [
                    'success' => true,
                    'data'    => $responseData,
                    'message' => 'Transaksi berhasil dibuat.',
                ];
            }

            $message = $responseData['message'] ?? $responseData['error'] ?? 'Gagal membuat transaksi Pakasir.';

            return [
                'success' => false,
                'data'    => $responseData ?? [],
                'message' => $message,
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('[Pakasir] createTransaction connection error', [
                'merchant_ref' => $body['merchant_ref'],
                'error'        => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'data'    => [],
                'message' => 'Tidak dapat terhubung ke server Pakasir: ' . $e->getMessage(),
            ];
        } catch (\Exception $e) {
            Log::error('[Pakasir] createTransaction unexpected error', [
                'merchant_ref' => $body['merchant_ref'],
                'error'        => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'data'    => [],
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Cek status transaksi berdasarkan transaction_id dari Pakasir.
     *
     * @param string $transactionId  — pakasir_transaction_id
     * @return array ['success'=>bool, 'data'=>array, 'message'=>string]
     */
    public function checkStatus(string $transactionId): array
    {
        Log::info('[Pakasir] checkStatus request', ['transaction_id' => $transactionId]);

        try {
            $response = Http::withHeaders([
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->timeout(15)->get($this->baseUrl . '/transactions/' . $transactionId);

            $responseData = $response->json();

            Log::info('[Pakasir] checkStatus response', [
                'http_status'   => $response->status(),
                'transaction_id'=> $transactionId,
                'body'          => $responseData,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data'    => $responseData,
                    'message' => 'Status berhasil diambil.',
                ];
            }

            return [
                'success' => false,
                'data'    => $responseData ?? [],
                'message' => $responseData['message'] ?? 'Gagal mengambil status transaksi.',
            ];
        } catch (\Exception $e) {
            Log::error('[Pakasir] checkStatus error', [
                'transaction_id' => $transactionId,
                'error'          => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'data'    => [],
                'message' => 'Gagal cek status: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verifikasi webhook signature dari Pakasir.
     * Format: HMAC-SHA256(merchant_ref + amount + status, api_key)
     *
     * @param array  $payload    — seluruh body webhook
     * @param string $signature  — nilai dari header X-Pakasir-Signature
     * @return bool
     */
    public function verifyWebhookSignature(array $payload, string $signature): bool
    {
        $merchantRef = $payload['merchant_ref'] ?? '';
        $amount      = $payload['amount']       ?? '';
        $status      = $payload['status']       ?? '';

        $secret       = config('pakasir.webhook_secret', $this->apiKey);
        $dataToSign   = $merchantRef . $amount . $status;
        $expected     = hash_hmac('sha256', $dataToSign, $secret);

        $valid = hash_equals($expected, strtolower($signature));

        if (!$valid) {
            Log::warning('[Pakasir] verifyWebhookSignature FAILED', [
                'merchant_ref' => $merchantRef,
                'status'       => $status,
                'received_sig' => $signature,
                'expected_sig' => $expected,
            ]);
        }

        return $valid;
    }

    /**
     * Map status Pakasir ke status internal ('paid'|'failed'|'expired'|'pending').
     */
    public function mapStatus(string $pakasirStatus): string
    {
        return match (strtolower($pakasirStatus)) {
            'paid', 'success', 'settlement', 'capture' => 'paid',
            'failed', 'cancel', 'deny', 'refund'       => 'failed',
            'expired', 'expire'                         => 'expired',
            default                                     => 'pending',
        };
    }

    /**
     * Getter untuk merchant slug (dipakai controller).
     */
    public function getMerchantSlug(): string
    {
        return $this->merchantSlug;
    }
}   