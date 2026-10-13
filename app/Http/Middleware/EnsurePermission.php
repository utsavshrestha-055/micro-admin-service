<?php

namespace App\Http\Middleware;

use App\Grpc\GrpcRequestException;
use App\Services\Authorization\PermissionAuthorizationService;
use Closure;
use Illuminate\Http\Request;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    public function __construct(private PermissionAuthorizationService $permissionAuthorizationService) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $token = $request->session()->get('admin.access_token');

        if (! is_string($token) || $token === '') {
            return redirect()->route('login');
        }

        try {
            $allowed = $this->permissionAuthorizationService->allows($token, $permission);
        } catch (GrpcRequestException $exception) {
            if ($exception->grpcStatus === 'UNAUTHENTICATED') {
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('status', 'Your session expired. Please sign in again.');
            }

            abort(503, 'Authorization is temporarily unavailable. Please try again.');
        } catch (RuntimeException) {
            abort(503, 'Authorization is temporarily unavailable. Please try again.');
        }

        if (! $allowed) {
            abort(403);
        }

        return $next($request);
    }
}
