<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\UserPackageFeature;
use Illuminate\Support\Facades\DB;

class CheckFeatureLimit
{
    public function handle($request, Closure $next, string $featureKey)
    {
        $userId =  $request->user_id;

        $feature = UserPackageFeature::query()
            ->where('user_id', $userId)
            ->whereHas('feature', function ($q) use ($featureKey) {
                $q->where('key', $featureKey);
            })
            ->lockForUpdate()
            ->first();


        if (!$feature || $feature->remaining_count <= 0) {
            return response()->json([
                'message' => 'Feature limit exceeded'
            ], 403);
        }

        return $next($request);
    }
}
