<?php

namespace App\Http\Service\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class KashierPaymentService
{
    /**
     * Create Kashier payment session
     *
     * @param float  $amount
     * @param string $customerEmail
     * @param string $transactionId
     * @return string|null
     */
    public function createSession(
        float $amount,
        string $customerEmail,
        string $transactionId
    ): ?string {
        try {
            $response = Http::withHeaders([
                'Authorization' => config('kashier.secret_key'),
                'api-key'       => config('kashier.api_key'),
                'Content-Type'  => 'application/json',
            ])->post('https://test-api.kashier.io/v3/payment/sessions', [
                'expireAt' => now()->addMinutes(30)->toISOString(),
                'amount'   => number_format($amount, 2, '.', ''),
                'currency' => 'EGP',
                'order'    => $transactionId,
                'merchantId' => config('kashier.merchant_id'),
                'merchantRedirect' => route('kashier.success'),
                'failureRedirect'  => route('kashier.failure'),
                'serverWebhook'    => route('kashier.webhook'),
                'allowedMethods'   => 'card,wallet',
                'interactionSource' => 'ECOMMERCE',
                'enable3DS' => true,

                'customer' => [
                    'email'     => $customerEmail,
                    'reference' => 'CUST-' . Str::uuid(),
                ],
            ]);

            if (!$response->successful()) {
                Log::error('Kashier API Error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            $sessionUrl = $response->json('sessionUrl');

            if (!$sessionUrl) {
                Log::error('Kashier sessionUrl missing', [
                    'response' => $response->json(),
                ]);
                return null;
            }

            return $sessionUrl;
        } catch (\Throwable $e) {
            Log::error('Kashier createSession exception', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
