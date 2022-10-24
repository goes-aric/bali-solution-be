<?php
namespace App\Http\Services\v1\Settings\User;

class PermissionServices
{
    public function appPermissions(){
        $permissions = [
            [
                'permission_name'   => 'View',
                'key'               => 'view',
                'group_key'         => 'Suppliers',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'Create',
                'key'               => 'create',
                'group_key'         => 'Suppliers',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'Edit',
                'key'               => 'edit',
                'group_key'         => 'Suppliers',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'Delete',
                'key'               => 'delete',
                'group_key'         => 'Suppliers',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'View',
                'key'               => 'view',
                'group_key'         => 'Units',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'Create',
                'key'               => 'create',
                'group_key'         => 'Units',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'Edit',
                'key'               => 'edit',
                'group_key'         => 'Units',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'Delete',
                'key'               => 'delete',
                'group_key'         => 'Units',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'View',
                'key'               => 'view',
                'group_key'         => 'Seasons',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'Create',
                'key'               => 'create',
                'group_key'         => 'Seasons',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'Edit',
                'key'               => 'edit',
                'group_key'         => 'Seasons',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'Delete',
                'key'               => 'delete',
                'group_key'         => 'Seasons',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'View',
                'key'               => 'view',
                'group_key'         => 'Collections',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'Create',
                'key'               => 'create',
                'group_key'         => 'Collections',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'Edit',
                'key'               => 'edit',
                'group_key'         => 'Collections',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'Delete',
                'key'               => 'delete',
                'group_key'         => 'Collections',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'View',
                'key'               => 'view',
                'group_key'         => 'Factory',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'Create',
                'key'               => 'create',
                'group_key'         => 'Factory',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'Edit',
                'key'               => 'edit',
                'group_key'         => 'Factory',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'Delete',
                'key'               => 'delete',
                'group_key'         => 'Factory',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'View',
                'key'               => 'view',
                'group_key'         => 'Raw Materials',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'Create',
                'key'               => 'create',
                'group_key'         => 'Raw Materials',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'Edit',
                'key'               => 'edit',
                'group_key'         => 'Raw Materials',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'Delete',
                'key'               => 'delete',
                'group_key'         => 'Raw Materials',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'View',
                'key'               => 'view',
                'group_key'         => 'Products',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'Create',
                'key'               => 'create',
                'group_key'         => 'Products',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'Edit',
                'key'               => 'edit',
                'group_key'         => 'Products',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'Delete',
                'key'               => 'delete',
                'group_key'         => 'Products',
                'master_group'      => 'Data Masters',
            ],
            [
                'permission_name'   => 'View',
                'key'               => 'view',
                'group_key'         => 'Customer Orders',
                'master_group'      => 'Transactions',
            ],
            [
                'permission_name'   => 'Create',
                'key'               => 'create',
                'group_key'         => 'Customer Orders',
                'master_group'      => 'Transactions',
            ],
            [
                'permission_name'   => 'Edit',
                'key'               => 'edit',
                'group_key'         => 'Customer Orders',
                'master_group'      => 'Transactions',
            ],
            [
                'permission_name'   => 'Delete',
                'key'               => 'delete',
                'group_key'         => 'Customer Orders',
                'master_group'      => 'Transactions',
            ],
            [
                'permission_name'   => 'Validate',
                'key'               => 'validate',
                'group_key'         => 'Customer Orders',
                'master_group'      => 'Transactions',
            ],
            [
                'permission_name'   => 'Cancel Validate',
                'key'               => 'cancel',
                'group_key'         => 'Customer Orders',
                'master_group'      => 'Transactions',
            ],
            [
                'permission_name'   => 'View',
                'key'               => 'view',
                'group_key'         => 'Materials Calculation',
                'master_group'      => 'Process',
            ],
            [
                'permission_name'   => 'Create',
                'key'               => 'create',
                'group_key'         => 'Materials Calculation',
                'master_group'      => 'Process',
            ],
            [
                'permission_name'   => 'Edit',
                'key'               => 'edit',
                'group_key'         => 'Materials Calculation',
                'master_group'      => 'Process',
            ],
            [
                'permission_name'   => 'Delete',
                'key'               => 'delete',
                'group_key'         => 'Materials Calculation',
                'master_group'      => 'Process',
            ],
            [
                'permission_name'   => 'Validate',
                'key'               => 'validate',
                'group_key'         => 'Materials Calculation',
                'master_group'      => 'Process',
            ],
            [
                'permission_name'   => 'Cancel Validate',
                'key'               => 'cancel',
                'group_key'         => 'Materials Calculation',
                'master_group'      => 'Process',
            ],
            [
                'permission_name'   => 'View',
                'key'               => 'view',
                'group_key'         => 'Order Recapitulation',
                'master_group'      => 'Process',
            ],
            [
                'permission_name'   => 'View',
                'key'               => 'view',
                'group_key'         => 'Material Analysis',
                'master_group'      => 'Process',
            ],
            [
                'permission_name'   => 'View',
                'key'               => 'view',
                'group_key'         => 'Purchase Order',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'Create',
                'key'               => 'create',
                'group_key'         => 'Purchase Order',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'Edit',
                'key'               => 'edit',
                'group_key'         => 'Purchase Order',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'Delete',
                'key'               => 'delete',
                'group_key'         => 'Purchase Order',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'Validate',
                'key'               => 'validate',
                'group_key'         => 'Purchase Order',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'Cancel Validate',
                'key'               => 'cancel',
                'group_key'         => 'Purchase Order',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'View',
                'key'               => 'view',
                'group_key'         => 'Receiving',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'Create',
                'key'               => 'create',
                'group_key'         => 'Receiving',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'Edit',
                'key'               => 'edit',
                'group_key'         => 'Receiving',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'Delete',
                'key'               => 'delete',
                'group_key'         => 'Receiving',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'Validate',
                'key'               => 'validate',
                'group_key'         => 'Receiving',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'Cancel Validate',
                'key'               => 'cancel',
                'group_key'         => 'Receiving',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'View',
                'key'               => 'view',
                'group_key'         => 'Adjustments',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'Create',
                'key'               => 'create',
                'group_key'         => 'Adjustments',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'Edit',
                'key'               => 'edit',
                'group_key'         => 'Adjustments',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'Delete',
                'key'               => 'delete',
                'group_key'         => 'Adjustments',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'Validate',
                'key'               => 'validate',
                'group_key'         => 'Adjustments',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'Cancel Validate',
                'key'               => 'cancel',
                'group_key'         => 'Adjustments',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'View',
                'key'               => 'view',
                'group_key'         => 'Transfer Stock',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'Create',
                'key'               => 'create',
                'group_key'         => 'Transfer Stock',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'Edit',
                'key'               => 'edit',
                'group_key'         => 'Transfer Stock',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'Delete',
                'key'               => 'delete',
                'group_key'         => 'Transfer Stock',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'Validate',
                'key'               => 'validate',
                'group_key'         => 'Transfer Stock',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'Cancel Validate',
                'key'               => 'cancel',
                'group_key'         => 'Transfer Stock',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'View',
                'key'               => 'view',
                'group_key'         => 'Stock Opname',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'Create',
                'key'               => 'create',
                'group_key'         => 'Stock Opname',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'Edit',
                'key'               => 'edit',
                'group_key'         => 'Stock Opname',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'Delete',
                'key'               => 'delete',
                'group_key'         => 'Stock Opname',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'Validate',
                'key'               => 'validate',
                'group_key'         => 'Stock Opname',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'Cancel Validate',
                'key'               => 'cancel',
                'group_key'         => 'Stock Opname',
                'master_group'      => 'Inventory',
            ],
            [
                'permission_name'   => 'View',
                'key'               => 'view',
                'group_key'         => 'Material Reports',
                'master_group'      => 'Reports',
            ],
            [
                'permission_name'   => 'View',
                'key'               => 'view',
                'group_key'         => 'Order Reports',
                'master_group'      => 'Reports',
            ],
            [
                'permission_name'   => 'View',
                'key'               => 'view',
                'group_key'         => 'Default Variable',
                'master_group'      => 'Settings',
            ],
            [
                'permission_name'   => 'Save',
                'key'               => 'save',
                'group_key'         => 'Default Variable',
                'master_group'      => 'Settings',
            ],
            [
                'permission_name'   => 'View',
                'key'               => 'view',
                'group_key'         => 'Exchange Rate',
                'master_group'      => 'Settings',
            ],
            [
                'permission_name'   => 'Create',
                'key'               => 'create',
                'group_key'         => 'Exchange Rate',
                'master_group'      => 'Settings',
            ],
            [
                'permission_name'   => 'Edit',
                'key'               => 'edit',
                'group_key'         => 'Exchange Rate',
                'master_group'      => 'Settings',
            ],
            [
                'permission_name'   => 'Delete',
                'key'               => 'delete',
                'group_key'         => 'Exchange Rate',
                'master_group'      => 'Settings',
            ],
            [
                'permission_name'   => 'View',
                'key'               => 'view',
                'group_key'         => 'Adjustment Reasons',
                'master_group'      => 'Settings',
            ],
            [
                'permission_name'   => 'Create',
                'key'               => 'create',
                'group_key'         => 'Adjustment Reasons',
                'master_group'      => 'Settings',
            ],
            [
                'permission_name'   => 'Edit',
                'key'               => 'edit',
                'group_key'         => 'Adjustment Reasons',
                'master_group'      => 'Settings',
            ],
            [
                'permission_name'   => 'Delete',
                'key'               => 'delete',
                'group_key'         => 'Adjustment Reasons',
                'master_group'      => 'Settings',
            ],
            [
                'permission_name'   => 'View',
                'key'               => 'view',
                'group_key'         => 'Users',
                'master_group'      => 'Settings',
            ],
            [
                'permission_name'   => 'Create',
                'key'               => 'create',
                'group_key'         => 'Users',
                'master_group'      => 'Settings',
            ],
            [
                'permission_name'   => 'Edit',
                'key'               => 'edit',
                'group_key'         => 'Users',
                'master_group'      => 'Settings',
            ],
            [
                'permission_name'   => 'Delete',
                'key'               => 'delete',
                'group_key'         => 'Users',
                'master_group'      => 'Settings',
            ],
            [
                'permission_name'   => 'View',
                'key'               => 'view',
                'group_key'         => 'Activity Logs',
                'master_group'      => 'Settings',
            ]
        ];

        return $permissions;
    }

    public function getPermissions($groupKey, $key){
        $permissions = [];
        foreach ($this->appPermissions() as $item) {
            if ($item['group_key'] === $groupKey && $item['key'] === $key) {
                $permissions = [
                    'permission_name'   => $item['permission_name'],
                    'key'               => $item['key'],
                    'group_key'         => $item['group_key'],
                    'master_group'      => $item['master_group']
                ];
            }
        }

        return $permissions;
    }
}
