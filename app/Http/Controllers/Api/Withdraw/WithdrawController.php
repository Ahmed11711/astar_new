<?php

namespace App\Http\Controllers\Api\Withdraw;

use App\Models\withdraw;
use App\Models\userBalance;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Withdraw\WithdrawRequest;
use App\Http\Requests\Api\Withdraw\AddBalanceRequest;

class WithdrawController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        // $user = auth()->user();
        $withdrawals = withdraw::get();

        return $this->successResponse($withdrawals, 'User withdrawals retrieved successfully.');
    }

    public function withdraw(WithdrawRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();

         if ($data['type_withdraw'] === 'affiliate') {
            $balanceType = 'affiliate_balance';
        } else {
            $balanceType = 'balance';
        }

 
        $userBalance = userBalance::where('user_id', $user->id)->first();

        if (!$userBalance || $userBalance[$balanceType] < $data['amount']) {
            return $this->errorResponse('Insufficient balance for this withdrawal request.', 400);
        }
        // Deduct the amount from the user's balance
        $userBalance[$balanceType] -= $data['amount'];
        $userBalance->save();

       
        $data['user_id'] = $user->id;
         $withdraw = withdraw::create($data);

        return $this->successResponse($data, 'Withdrawal request submitted successfully.');
    }

    public function addBalance(AddBalanceRequest $request)
    {
    $user = auth()->user();
    $data = $request->validated();

     $userBalance = userBalance::firstOrCreate(
        ['user_id' => $user->id],
        ['affiliate_balance' => 0, 'balance' => 0]
    );

     $balanceType = $data['type_balance'];

     $userBalance->increment($balanceType, $data['amount']);
    return $this->successResponse($userBalance, 'Balance added successfully.');

        

    }
}
