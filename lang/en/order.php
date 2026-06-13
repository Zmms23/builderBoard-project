<?php

return [
    'actions' => [
        'approve' => [
            'label' => 'Approve',
            'success' => 'Order approved',
        ],
        'reject' => [
            'label' => 'Reject',
            'success' => 'Order rejected',
        ],
    ],
    'columns' => [
        'assigned_user' => 'Responsible',
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
        'assigned_user' => 'Responsible member',
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
        'assigned_user' => 'Responsible',
        'client' => 'Client',
        'project' => 'Project',
        'status' => 'Status',
    ],
    'help' => [
        'assigned_user' => 'Workers only see orders assigned to them.',
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
