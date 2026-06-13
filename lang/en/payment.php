<?php

return [
    'columns' => [
        'amount' => 'Amount',
        'client' => 'Client',
        'created_at' => 'Created at',
        'method' => 'Method',
        'order' => 'Order',
        'paid_at' => 'Paid at',
        'project' => 'Project',
        'reference' => 'Reference',
        'status' => 'Status',
    ],
    'fields' => [
        'amount' => 'Amount',
        'client' => 'Client',
        'method' => 'Payment method',
        'notes' => 'Notes',
        'order' => 'Order',
        'paid_at' => 'Paid at',
        'project' => 'Project',
        'reference' => 'Reference',
        'status' => 'Status',
    ],
    'filters' => [
        'client' => 'Client',
        'method' => 'Method',
        'order' => 'Order',
        'status' => 'Status',
    ],
    'methods' => [
        'bank_transfer' => 'Bank transfer',
        'card' => 'Card',
        'cash' => 'Cash',
        'other' => 'Other',
    ],
    'navigation' => [
        'badge' => 'Payment count',
        'plural' => 'Payments',
        'singular' => 'Payment',
    ],
    'sections' => [
        'details' => 'Payment details',
    ],
    'statuses' => [
        'failed' => 'Failed',
        'paid' => 'Paid',
        'pending' => 'Pending',
        'refunded' => 'Refunded',
    ],
];
