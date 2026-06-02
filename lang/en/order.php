<?php

return [
    'columns' => [
        'client' => 'Client',
        'created_at' => 'Created at',
        'estimated_price' => 'Estimated price',
        'number' => 'Number',
        'status' => 'Status',
        'title' => 'Title',
    ],
    'fields' => [
        'client' => 'Client',
        'estimated_price' => 'Estimated price',
        'notes' => 'Notes',
        'number' => 'Order number',
        'status' => 'Status',
        'title' => 'Order title',
    ],
    'filters' => [
        'client' => 'Client',
        'status' => 'Status',
    ],
    'navigation' => [
        'badge' => 'Order count',
        'plural' => 'Orders',
        'singular' => 'Order',
    ],
    'sections' => [
        'details' => 'Order details',
    ],
    'statuses' => [
        'approved' => 'Approved',
        'draft' => 'Draft',
        'pending' => 'Pending',
        'rejected' => 'Rejected',
    ],
];
