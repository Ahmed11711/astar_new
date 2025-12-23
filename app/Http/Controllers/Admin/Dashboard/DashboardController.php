<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Service\Admin\DashboardStatsService;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(DashboardStatsService $service)
    {
        $data = Cache::remember(
            'admin_dashboard_stats',
            now()->addMinutes(5),
            fn() => $service->get()
        );

        return response()->json($service->get());
    }
}
