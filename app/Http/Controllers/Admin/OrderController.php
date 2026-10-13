<?php

namespace App\Http\Controllers\Admin;

use App\Grpc\GrpcRequestException;
use App\Http\Controllers\Controller;
use App\Services\Commerce\CommerceManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class OrderController extends Controller
{
    private const STATUSES = ['PENDING' => 0, 'CONFIRMED' => 1, 'PAID' => 2, 'SHIPPED' => 3, 'DELIVERED' => 4, 'CANCELLED' => 5, 'REFUNDED' => 6];

    public function index(Request $request, CommerceManagementService $commerce): View
    {
        $filters = ['pageSize' => 25, 'pageToken' => (string) $request->query('page_token', '')];

        foreach (['user_id' => 'userId', 'created_after' => 'createdAfter', 'created_before' => 'createdBefore'] as $query => $field) {
            if ($request->filled($query)) {
                $filters[$field] = $request->query($query);
            }
        }

        if ($request->filled('status') && array_key_exists((string) $request->query('status'), self::STATUSES)) {
            $filters['status'] = self::STATUSES[(string) $request->query('status')];
        }

        try {
            $response = $commerce->listOrders($filters);
            $orders = $response['orders'] ?? [];
        } catch (RuntimeException) {
            abort(503, 'The order service is temporarily unavailable.');
        }

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => array_keys(self::STATUSES),
            'nextPageToken' => $response['next_page_token'] ?? '',
        ]);
    }

    public function show(string $orderId, CommerceManagementService $commerce): View
    {
        try {
            $order = $commerce->getOrder($orderId)['order'] ?? null;
        } catch (GrpcRequestException $exception) {
            if ($exception->grpcStatus === 'NOT_FOUND') {
                abort(404);
            }

            abort(503, 'The order service is temporarily unavailable.');
        } catch (RuntimeException) {
            abort(503, 'The order service is temporarily unavailable.');
        }

        abort_unless(is_array($order), 404);

        return view('admin.orders.show', ['order' => $order, 'statuses' => array_keys(self::STATUSES)]);
    }

    public function updateStatus(Request $request, string $orderId, CommerceManagementService $commerce): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', array_keys(self::STATUSES))],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);
        $actorId = data_get($request->session()->get('admin.user'), 'id');

        if (! is_string($actorId) || $actorId === '') {
            abort(401);
        }

        try {
            $commerce->updateOrderStatus($orderId, self::STATUSES[$validated['status']], $actorId, $validated['note'] ?? '');
        } catch (RuntimeException) {
            return back()->withErrors(['order' => 'The order status could not be updated. Check the allowed status transitions and try again.'])->withInput();
        }

        return redirect()->route('admin.orders.show', $orderId)->with('status', 'Order status updated.');
    }
}
