<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class IntegrationPlaceholderController extends Controller
{
    public function __invoke(string $section): View
    {
        $sections = [
            'products' => ['Products', 'Product catalog data is not available until a commerce API is connected.'],
            'categories' => ['Categories', 'Category data is not available until a catalog API is connected.'],
            'merchants' => ['Merchants', 'Merchant data is not available until a merchant API is connected.'],
            'orders' => ['Orders', 'Order data is not available until an order API is connected.'],
            'customers' => ['Customers', 'Customer data is not available until a customer API is connected.'],
            'inventory' => ['Inventory', 'Inventory data is not available until an inventory API is connected.'],
            'reports' => ['Reports', 'Reports will appear when operational data sources are connected.'],
            'settings' => ['Settings', 'Platform settings are not available until a settings API is connected.'],
            'audit-logs' => ['Audit logs', 'Audit events are not available until an audit API is connected.'],
        ];

        abort_unless(isset($sections[$section]), 404);

        return view('admin.integration-placeholder', [
            'title' => $sections[$section][0],
            'message' => $sections[$section][1],
        ]);
    }
}
