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

    public static array $blogCatgoryPermissions = [
        'group_name' => 'blog category',
        'permissions' => [
            'blog.category.view',
            'blog.category.create',
            'blog.category.translate',
            'blog.category.store',
            'blog.category.edit',
            'blog.category.update',
            'blog.category.delete',
        ],
    ];

    public static array $blogPermissions = [
        'group_name' => 'blog',
        'permissions' => [
            'blog.view',
            'blog.create',
            'blog.translate',
            'blog.store',
            'blog.edit',
            'blog.update',
            'blog.delete',
        ],
    ];

    public static array $blogCommentPermissions = [
        'group_name' => 'blog comment',
        'permissions' => [
            'blog.comment.view',
            'blog.comment.update',
            'blog.comment.delete',
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

    public static array $basicPaymentPermissions = [
        'group_name' => 'basic payment',
        'permissions' => [
            'basic.payment.view',
            'basic.payment.update',
        ],
    ];

    public static array $contectMessagePermissions = [
        'group_name' => 'contect message',
        'permissions' => [
            'contect.message.view',
            'contect.message.delete',
        ],
    ];

    public static array $currencyPermissions = [
        'group_name' => 'currency',
        'permissions' => [
            'currency.view',
            'currency.create',
            'currency.store',
            'currency.edit',
            'currency.update',
            'currency.delete',
        ],
    ];

    public static array $mediaPermissions = [
        'group_name' => 'media',
        'permissions' => [
            'media.view',
            'media.create',
            'media.store',
            'media.edit',
            'media.update',
            'media.delete',
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

    public static array $languagePermissions = [
        'group_name' => 'language',
        'permissions' => [
            'language.view',
            'language.create',
            'language.store',
            'language.edit',
            'language.update',
            'language.delete',
            'language.translate',
            'language.single.translate',
        ],
    ];

    public static array $menuPermissions = [
        'group_name' => 'menu builder',
        'permissions' => [
            'menu.view',
            'menu.create',
            'menu.store',
            'menu.edit',
            'menu.update',
            'menu.delete',
        ],
    ];

    public static array $pagePermissions = [
        'group_name' => 'page builder',
        'permissions' => [
            'page.view',
            'page.create',
            'page.store',
            'page.edit',
            'page.component.add',
            'page.update',
            'page.delete',
        ],
    ];

    public static array $subscriptionPermissions = [
        'group_name' => 'subscription',
        'permissions' => [
            'subscription.view',
            'subscription.create',
            'subscription.store',
            'subscription.edit',
            'subscription.update',
            'subscription.delete',
        ],
    ];

    public static array $paymentPermissions = [
        'group_name' => 'payment',
        'permissions' => [
            'payment.view',
            'payment.update',
        ],
    ];

    public static array $supportTicketPermissions = [
        'group_name' => 'support ticket',
        'permissions' => [
            'support.ticket.view',
            'support.ticket.manage',
            'support.ticket.delete',
            'support.ticket.close',
        ],
    ];

    public static array $newsletterPermissions = [
        'group_name' => 'newsletter',
        'permissions' => [
            'newsletter.view',
            'newsletter.mail',
            'newsletter.delete',
        ],
    ];

    public static array $testimonialPermissions = [
        'group_name' => 'testimonial',
        'permissions' => [
            'testimonial.view',
            'testimonial.create',
            'testimonial.translate',
            'testimonial.store',
            'testimonial.edit',
            'testimonial.update',
            'testimonial.delete',
        ],
    ];

    public static array $faqPermissions = [
        'group_name' => 'faq',
        'permissions' => [
            'faq.view',
            'faq.create',
            'faq.translate',
            'faq.store',
            'faq.edit',
            'faq.update',
            'faq.delete',
        ],
    ];

    public static array $addonsPermissions = [
        'group_name' => 'Addons',
        'permissions' => [
            'addon.view',
            'addon.install',
            'addon.update',
            'addon.status.change',
            'addon.remove',
        ],
    ];

    //if you have custom role and you want to set custom permission for the role then use a prefix (ie. others_) of the role and modifiy this inside the get method

    // public static array $others_ExamplePermissions = [
    //     'group_name' => 'others example',
    //     'permissions' => [
    //         'others.example.example',
    //     ],
    // ];

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

    // ======================= customize your new permissions here ============================= \\
    // private static function getOtherPermissions(): array
    // {
    //     $reflection = new ReflectionClass(__TRAIT__);
    //     $properties = $reflection->getStaticProperties();

    //     // Filter properties with prefix 'others_'
    //     $permissions = [];

    //     // Loop through each property
    //     foreach ($properties as $propertyName => $value) {
    //         // Check if the property name starts with 'user__' and the value is an array
    //         if (strpos($propertyName, 'user__') === 0 && is_array($value)) {
    //             // Add group_name and permissions to the permissions array
    //             $permissions[] = [
    //                 'group_name' => $value['group_name'],
    //                 'permissions' => $value['permissions'],
    //             ];
    //         }
    //     }

    //     return $permissions;
    // }
}
