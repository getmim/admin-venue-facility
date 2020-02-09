<?php

return [
    '__name' => 'admin-venue-facility',
    '__version' => '0.0.3',
    '__git' => 'git@github.com:getmim/admin-venue-facility.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'http://iqbalfn.com/'
    ],
    '__files' => [
        'modules/admin-venue-facility' => ['install','update','remove'],
        'theme/admin/venue/facility' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [
            [
                'admin' => NULL
            ],
            [
                'lib-formatter' => NULL
            ],
            [
                'lib-form' => NULL
            ],
            [
                'lib-pagination' => NULL
            ],
            [
                'venue-facility' => NULL
            ],
            [
                'admin-venue' => NULL 
            ]
        ],
        'optional' => []
    ],
    'autoload' => [
        'classes' => [
            'AdminVenueFacility\\Controller' => [
                'type' => 'file',
                'base' => 'modules/admin-venue-facility/controller'
            ]
        ],
        'files' => []
    ],
    'routes' => [
        'admin' => [
            'adminVenueFacility' => [
                'path' => [
                    'value' => '/venue/facility'
                ],
                'method' => 'GET',
                'handler' => 'AdminVenueFacility\\Controller\\Facility::index'
            ],
            'adminVenueFacilityEdit' => [
                'path' => [
                    'value' => '/venue/facility/(:id)',
                    'params' => [
                        'id'  => 'number'
                    ]
                ],
                'method' => 'GET|POST',
                'handler' => 'AdminVenueFacility\\Controller\\Facility::edit'
            ],
            'adminVenueFacilityRemove' => [
                'path' => [
                    'value' => '/venue/facility/(:id)/remove',
                    'params' => [
                        'id'  => 'number'
                    ]
                ],
                'method' => 'GET',
                'handler' => 'AdminVenueFacility\\Controller\\Facility::remove'
            ]
        ]
    ],
    'adminUi' => [
        'sidebarMenu' => [
            'items' => [
                'venue' => [
                    'label' => 'Venue',
                    'icon' => '<i class="fas fa-map-marker-alt"></i>',
                    'priority' => 0,
                    'children' => [
                        'facility' => [
                            'label' => 'Facility',
                            'icon'  => '<i></i>',
                            'route' => ['adminVenueFacility'],
                            'perms' => 'manage_venue_facility'
                        ]
                    ]
                ]
            ]
        ]
    ],
    'libForm' => [
        'forms' => [
            'admin.venue.edit' => [
                'facility' => [
                    'label' => 'Facility',
                    'type' => 'checkbox-group',
                    'rules' => []
                ]
            ],
            'admin.venue-facility.edit' => [
                'name' => [
                    'label' => 'Name',
                    'type' => 'text',
                    'rules' => [
                        'required' => true,
                        'unique' => [
                            'model' => 'VenueFacility\\Model\\VenueFacility',
                            'field' => 'name',
                            'self' => [
                                'service' => 'req.param.id',
                                'field' => 'id'
                            ]
                        ]
                    ]
                ]
            ],
            'admin.venue-facility.index' => [
                'q' => [
                    'label' => 'Search',
                    'type' => 'search',
                    'nolabel' => true,
                    'rules' => []
                ]
            ]
        ]
    ]
];