<?php

namespace App\Traits;

use ReflectionClass;

trait PermissionsTrait
{
    public static array $dashboardPermissions = [
        'group_name' => 'dashboard',
        'permissions' => [
            'dashboard.view',
        ],
    ];

    public static array $adminProfilePermissions = [
        'group_name' => 'admin profile',
        'permissions' => [
            'admin.profile.view',
            'admin.profile.edit',
            'admin.profile.update',
            'admin.profile.delete',
        ],
    ];

    public static array $adminPermissions = [
        'group_name' => 'admin',
        'permissions' => [
            'admin.view',
            'admin.create',
            'admin.store',
            'admin.edit',
            'admin.update',
            'admin.delete',
        ],
    ];


    public static array $rolePermissions = [
        'group_name' => 'role',
        'permissions' => [
            'role.view',
            'role.create',
            'role.store',
            'role.assign',
            'role.edit',
            'role.update',
            'role.delete',
        ],
    ];

    public static array $settingPermissions = [
        'group_name' => 'setting',
        'permissions' => [
            'setting.view',
            'setting.update',
        ],
    ];

    public static array $supplierPermissions = [
        'group_name' => 'supplier',
        'permissions' => [
            'supplier.view',
            'supplier.create',
            'supplier.store',
            'supplier.edit',
            'supplier.update',
            'supplier.delete',
            'supplier.advance',
            'supplier.status',
            'supplier.due.pay',
            'supplier.due.pay.list',
            'supplier.due.pay.delete',
            'supplier.purchase.list',
            'supplier.group',
            'supplier.group.save',
            'supplier.excel.download',
            'supplier.pdf.download',
        ],
    ];



    public static array $customerPermissions = [
        'group_name' => 'customer',
        'permissions' => [
            'customer.view',
            'customer.bulk.mail',
            'customer.create',
            'customer.store',
            'customer.edit',
            'customer.update',
            'customer.delete',
        ],
    ];


    // return super admin permission aka 'all permissions'
    private static function getSuperAdminPermissions(): array
    {
        $reflection = new ReflectionClass(__TRAIT__);
        $properties = $reflection->getStaticProperties();

        $permissions = [];
        foreach ($properties as $value) {
            if (is_array($value)) {
                $permissions[] = [
                    'group_name' => $value['group_name'],
                    'permissions' => (array) $value['permissions'],
                ];
            }
        }

        return $permissions;
    }
}
