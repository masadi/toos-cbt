<?php

return [
    'role_structure' => [
        'admin' => [
            'users' => 'c,r,u,d',
            'acl' => 'c,r,u,d',
            'profile' => 'r,u'
        ],
        'sekolah' => [
            'users' => 'c,r,u,d',
            'profile' => 'r,u'
        ],
        'ptk' => [
            'profile' => 'r,u'
        ],
        'proktor' => [
            'profile' => 'r,u'
        ],
        'peserta_didik' => [
            'profile' => 'r,u'
        ],
        'user' => [
            'profile' => 'r,u'
        ],
    ],
    'permission_structure' => [
        'cru_user' => [
            'profile' => 'c,r,u'
        ],
    ],
    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete'
    ]
];
