<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CreateAccountRequest;
use App\Http\Service\Auth\CreateAccountService;
use App\Http\Service\Payment\KashierPaymentService;
use App\Traits\ApiResponseTrait;

class CreateAccountController extends Controller
{
    use ApiResponseTrait;

    public function createAccount(
        CreateAccountRequest $request,
        CreateAccountService $service,
        KashierPaymentService $payment
    ) {
        $result = $service->execute($request->validated());

        $user = $result['user']; //
        $studentPackage = $result['studentPackage'];
        if ($studentPackage && $studentPackage->price > 0) {
            $paymentUrl = $payment->createSession(
                $studentPackage->price,
                $user->email,
                $studentPackage->transaction_id
            );

            return $this->successResponse([
                'payment_required' => true,
                'payment_url' => $paymentUrl,
                // 'transaction_id' => $studentPackage->transaction_id,
            ], 'Payment required');
        }

        return $this->successResponse([
            'payment_required' => false,
            'user' => $user,
        ], 'Account created successfully');
    }
}
