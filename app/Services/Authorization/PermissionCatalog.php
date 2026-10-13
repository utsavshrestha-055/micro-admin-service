<?php

namespace App\Services\Authorization;

class PermissionCatalog
{
    /** @return array<string, string> */
    public function all(): array
    {
        return [
            'admin.dashboard.view' => 'View the administration dashboard',
            'users.read' => 'View users and assigned roles',
            'users.manage' => 'Update and delete users',
            'roles.manage' => 'Manage roles and role permissions',
            'roles.assign' => 'Assign and unassign user roles',
            'permissions.manage' => 'Manage permissions',
            'products.read' => 'View products when the product service is connected',
            'products.manage' => 'Manage products when the product service is connected',
            'categories.read' => 'View categories when the catalog service is connected',
            'categories.manage' => 'Manage categories when the catalog service is connected',
            'merchants.read' => 'View merchants when the merchant service is connected',
            'merchants.manage' => 'Manage merchants when the merchant service is connected',
            'orders.read' => 'View orders when the order service is connected',
            'orders.manage' => 'Manage orders when the order service is connected',
            'customers.read' => 'View customers when the customer service is connected',
            'inventory.read' => 'View inventory when the inventory service is connected',
            'inventory.manage' => 'Manage inventory when the inventory service is connected',
            'reports.read' => 'View reports when the reporting service is connected',
            'settings.manage' => 'Manage platform settings when a settings service is connected',
            'audit_logs.read' => 'View audit logs when an audit service is connected',
        ];
    }
}
