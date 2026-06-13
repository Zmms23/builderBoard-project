<?php

return [
    'actions' => [
        'complete' => [
            'label' => 'Complete',
            'success' => 'Project completed',
        ],
        'open_workspace' => [
            'label' => 'Open workspace',
        ],
        'start' => [
            'label' => 'Start',
            'success' => 'Project started',
        ],
    ],
    'columns' => [
        'budget' => 'Budget',
        'client' => 'Client',
        'deadline' => 'Deadline',
        'order' => 'Order',
        'orders_count' => 'Orders',
        'paid' => 'Paid',
        'proof_uploads_count' => 'Proofs',
        'progress' => 'Progress',
        'remaining' => 'Remaining',
        'status' => 'Status',
        'title' => 'Title',
        'updated_at' => 'Updated at',
    ],
    'fields' => [
        'budget' => 'Budget',
        'client' => 'Client',
        'deadline' => 'Deadline',
        'notes' => 'Notes',
        'order' => 'Approved order',
        'progress' => 'Progress',
        'status' => 'Status',
        'title' => 'Project title',
    ],
    'filters' => [
        'status' => 'Status',
    ],
    'navigation' => [
        'badge' => 'Project count',
        'plural' => 'Projects',
        'singular' => 'Project',
    ],
    'sections' => [
        'details' => 'Project details',
    ],
    'statuses' => [
        'active' => 'Active',
        'canceled' => 'Canceled',
        'completed' => 'Completed',
        'on_hold' => 'On hold',
        'planning' => 'Planning',
    ],
    'timeline' => [
        'columns' => [
            'ends_at' => 'Ends at',
            'name' => 'Stage',
            'notes' => 'Notes',
            'sort' => 'Order',
            'starts_at' => 'Starts at',
            'status' => 'Status',
        ],
        'fields' => [
            'ends_at' => 'Ends at',
            'name' => 'Stage name',
            'notes' => 'Notes',
            'sort' => 'Order',
            'starts_at' => 'Starts at',
            'status' => 'Status',
        ],
        'filters' => [
            'status' => 'Status',
        ],
    ],
    'timeline_stage_statuses' => [
        'blocked' => 'Blocked',
        'completed' => 'Completed',
        'in_progress' => 'In progress',
        'pending' => 'Pending',
    ],
];
