<?php

return [
    'columns' => [
        'address' => 'Address',
        'email' => 'Email',
        'name' => 'Name',
        'notes' => 'Notes',
        'phone' => 'Phone',
        'status' => 'Status',
        'type' => 'Type',
        'updated_at' => 'Updated at',
    ],
    'fields' => [
        'address' => 'Address',
        'email' => 'Email',
        'name' => 'Client name',
        'notes' => 'Notes',
        'phone' => 'Phone',
        'status' => 'Status',
        'type' => 'Client type',
    ],
    'filters' => [
        'status' => 'Status',
        'type' => 'Client type',
    ],
    'navigation' => [
        'badge' => 'Client count',
        'plural' => 'Clients',
        'singular' => 'Client',
    ],
    'sections' => [
        'details' => 'Client details',
    ],
    'statuses' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'lead' => 'Lead',
    ],
    'types' => [
        'company' => 'Company',
        'person' => 'Person',
    ],
];
