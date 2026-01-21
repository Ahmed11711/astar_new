<?php

namespace App\Http\Controllers\Teacher\bugs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\bugsRequest;
use App\Models\bugs;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class bugsController extends Controller
{
    use ApiResponseTrait;
    public function index(Request $request)
    {
        $userId = $request->user_id;
        $bugs = bugs::where('user_id', $userId)
            ->latest()
            ->get();

        return $this->successResponse($bugs, "");
    }

    public function store(bugsRequest $request)
    {
        $userId = $request->user_id;
        $bug = bugs::create([
            'user_id'  => $userId,
            'question' => $request->question,
        ]);
        return $this->successResponse($bug, "");
    }
}
