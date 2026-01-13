<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class KashierPaymentController extends Controller
{
    public function create(Request $request)
    {
        $amount = '100.00';
        $currency = 'EGP';
        $merchantId = 'MID-41016-213';
        $secretKey = 'df974d751303a6d76a5637d19ca9a0f7$2c9243f4284be65f2055d390c1185f2fac0619b8c7a4ffee04af37e48051409836beda2dd93ebb72988ef55ad0d8e4ea';
        $apiKey = '9f78bd9d-fd4e-45fd-a7a6-93e3998b8712';

        $orderId = 'ORDER_' . Str::uuid();

        $successUrl = route('kashier.success');
        $failureUrl = route('kashier.failure');

        $response = Http::withHeaders([
            'Authorization' => $secretKey,
            'api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://test-api.kashier.io/v3/payment/sessions', [
            'expireAt' => now()->addMinutes(30)->toISOString(),
            'maxFailureAttempts' => 3,
            'amount' => $amount,
            'currency' => $currency,
            'order' => $orderId,
            'merchantId' => $merchantId,
            'merchantRedirect' => route('kashier.success'),
            'failureRedirect' => true,
            'serverWebhook' => route('kashier.webhook'),
            'allowedMethods' => 'card,wallet',
            'interactionSource' => 'ECOMMERCE',
            'enable3DS' => true,

            'customer' => [
                'email' => 'testuser@example.com',
                'reference' => 'CUST-' . Str::uuid()
            ]
        ]);


        $data = $response->json();
        Log::info('Kashier Payment Session Created:', $data);

        return response()->json([
            'status' => true,
            'sessionUrl' => $data['sessionUrl'] ?? null,
            'data' => $data
        ]);
    }

    public function handle(Request $request)
    {
        $payload = $request->all();
        Log::info('Kashier Webhook Payload:', $payload);

        $event = $payload['event'] ?? null;
        $data = $payload['data'] ?? [];

        if ($event === 'pay' && ($data['status'] ?? '') === 'SUCCESS') {
            Log::info('Payment Completed Successfully!', $data);
        } elseif ($event === 'refund') {
            Log::info('Refund Event Received', $data);
        } else {
            Log::info('Other Event Received', $payload);
        }

        return response()->json(['status' => 'ok'], 200);
    }














    public function success(Request $request)
    {
        $data = $request->all();
        Log::info('Kashier Payment Success Redirect:', $data);

        return response()->json([
            'message' => 'Payment Success (Redirect)',
            'data' => $data
        ]);
    }

    public function failure(Request $request)
    {
        $data = $request->all();
        Log::info('Kashier Payment Failure Redirect:', $data);

        return response()->json([
            'message' => 'Payment Failed (Redirect)',
            'data' => $data
        ]);
    }
}
