<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Auth\SessionService;
use App\Services\Authorization\PermissionAuthorizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use RuntimeException;

class AuthController extends Controller
{
    public function create(): View|RedirectResponse|Response
    {
        if (session()->has('admin.access_token')) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    public function store(Request $request, SessionService $sessionService, PermissionAuthorizationService $permissionAuthorizationService): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:1024'],
        ]);

        try {
            $sessionService->authenticate($validated['email'], $validated['password']);

            if (! $permissionAuthorizationService->allows((string) session('admin.access_token'), 'admin.dashboard.view')) {
                $sessionService->logout();

                return back()->withErrors([
                    'email' => 'This account does not have administrator access.',
                ])->withInput($request->only('email'));
            }
        } catch (RuntimeException) {
            if (session()->has('admin.access_token')) {
                $sessionService->logout();
            }

            return back()->withErrors([
                'email' => 'We could not sign you in. Check your credentials or try again shortly.',
            ])->withInput($request->only('email'));
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(SessionService $sessionService): RedirectResponse
    {
        $remoteLogoutSucceeded = $sessionService->logout();

        return redirect()->route('login')->with(
            'status',
            $remoteLogoutSucceeded ? 'You have been signed out.' : 'Your local session was cleared.',
        );
    }
}
