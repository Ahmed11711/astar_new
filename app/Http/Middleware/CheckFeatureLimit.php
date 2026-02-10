<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\UserPackageFeature;
use Illuminate\Support\Facades\DB;

class CheckFeatureLimit
{
    public function handle($request, Closure $next, string $featureKey)
    {
        $userId = $request->user_id;

        DB::beginTransaction();
        try {
            $feature = UserPackageFeature::query()
                ->where('user_id', $userId)
                ->where('active', 1)
                ->whereHas('feature', function ($q) use ($featureKey) {
                    $q->where('key_feature', $featureKey);
                })
                ->lockForUpdate()
                ->first();

            if (!$feature || $feature->remaining_count <= 0) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Feature limit exceeded'
                ], 403);
            }

            $feature->decrement('remaining_count');

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }

        return $next($request);
    }
}
