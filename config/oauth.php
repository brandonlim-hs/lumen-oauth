<?php

return [
    // OAuth token expiry in minutes
    'expire' => 60,

    // Config for oauth scopes
    'scopes' => [
        // Array of scopes with their respective description.
        'all' => [
            'create' => 'Crease access',
            'read' => 'Read access',
        ],

        // Array of client_ids mapped to their respective scopes.
        'clients' => [
            '3' => 'create read'
        ]
    ]
];