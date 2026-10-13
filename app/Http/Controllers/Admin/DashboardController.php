<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Auth\SessionService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class DashboardController extends Controller
{
    public function __invoke(Request $request, SessionService $sessionService): View
    {
        $serviceStatus = 'unavailable';

        try {
            $health = $sessionService->healthCheck();
            $serviceStatus = (string) ($health['status'] ?? 'unknown');
        } catch (RuntimeException) {
            // The dashboard remains useful when operational APIs are offline.
        }

        return view('admin.dashboard', [
            'user' => $request->session()->get('admin.user', []),
            'serviceStatus' => $serviceStatus,
        ]);
    }
}
