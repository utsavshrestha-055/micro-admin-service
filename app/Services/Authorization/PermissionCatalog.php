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
            'users.view_all' => 'View all users',
            'users.create' => 'Create users',
            'users.update' => 'Update users',
            'users.delete' => 'Delete users',
            'users.manage' => 'Update and delete users',
            'roles.manage' => 'Manage roles and role permissions',
            'roles.assign' => 'Assign and unassign user roles',
            'permissions.manage' => 'Manage permissions',
            'products.read' => 'View products in the catalog',
            'products.view_all' => 'View all products in the catalog',
            'products.create' => 'Create products in the catalog',
            'products.update' => 'Update products in the catalog',
            'products.delete' => 'Delete products from the catalog',
            'products.manage' => 'Create, update, and delete products in the catalog',
            'categories.read' => 'View product categories in the catalog',
            'categories.view_all' => 'View all product categories in the catalog',
            'categories.create' => 'Create product categories',
            'categories.update' => 'Update product categories',
            'categories.delete' => 'Delete product categories',
            'categories.manage' => 'Create, update, and delete product categories',
            'merchants.read' => 'View merchants when the merchant service is connected',
            'merchants.manage' => 'Manage merchants when the merchant service is connected',
            'orders.read' => 'View customer orders',
            'orders.view_all' => 'View all customer orders',
            'orders.update' => 'Update customer order statuses',
            'orders.manage' => 'Update customer order statuses',
            'customers.read' => 'View customers when the customer service is connected',
            'inventory.read' => 'View inventory when the inventory service is connected',
            'inventory.manage' => 'Manage inventory when the inventory service is connected',
            'reports.read' => 'View reports when the reporting service is connected',
            'settings.manage' => 'Manage platform settings when a settings service is connected',
            'audit_logs.read' => 'View audit logs when an audit service is connected',
        ];
    }

    /** @return array<string, string> */
    public function checked(): array
    {
        return array_intersect_key($this->all(), array_flip([
            'admin.dashboard.view',
            'users.read',
            'users.view_all',
            'users.create',
            'users.update',
            'users.delete',
            'users.manage',
            'roles.manage',
            'roles.assign',
            'permissions.manage',
            'products.read',
            'products.view_all',
            'products.create',
            'products.update',
            'products.delete',
            'products.manage',
            'categories.read',
            'categories.view_all',
            'categories.create',
            'categories.update',
            'categories.delete',
            'orders.read',
            'orders.view_all',
            'orders.update',
            'orders.manage',
            'merchants.read',
            'customers.read',
            'inventory.read',
            'reports.read',
            'settings.manage',
            'audit_logs.read',
        ]));
    }
}
