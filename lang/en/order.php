<?php

return [
    'columns' => [
        'client' => 'Client',
        'created_at' => 'Created at',
        'deadline' => 'Deadline',
        'estimated_price' => 'Estimated price',
        'number' => 'Number',
        'progress' => 'Progress',
        'project' => 'Project',
        'status' => 'Status',
        'title' => 'Title',
    ],
    'fields' => [
        'client' => 'Client',
        'deadline' => 'Deadline',
        'estimated_price' => 'Estimated price',
        'notes' => 'Notes',
        'number' => 'Order number',
        'progress' => 'Progress',
        'project' => 'Project',
        'status' => 'Status',
        'title' => 'Order title',
    ],
    'filters' => [
        'client' => 'Client',
        'project' => 'Project',
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
