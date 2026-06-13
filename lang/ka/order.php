<?php

return [
    'actions' => [
        'approve' => [
            'label' => 'დადასტურება',
            'success' => 'შეკვეთა დადასტურდა',
        ],
        'reject' => [
            'label' => 'უარყოფა',
            'success' => 'შეკვეთა უარყოფილია',
        ],
    ],
    'columns' => [
        'assigned_user' => 'პასუხისმგებელი',
        'deadline' => 'დედლაინი',
        'progress' => 'პროგრესი',
        'project' => 'პროექტი',
        'client' => 'კლიენტი',
        'created_at' => 'შეიქმნა',
        'estimated_price' => 'სავარაუდო ფასი',
        'number' => 'ნომერი',
        'status' => 'სტატუსი',
        'title' => 'სათაური',
    ],
    'fields' => [
        'assigned_user' => 'პასუხისმგებელი წევრი',
        'deadline' => 'დედლაინი',
        'progress' => 'პროგრესი',
        'project' => 'პროექტი',
        'client' => 'კლიენტი',
        'estimated_price' => 'სავარაუდო ფასი',
        'notes' => 'შენიშვნები',
        'number' => 'შეკვეთის ნომერი',
        'status' => 'სტატუსი',
        'title' => 'შეკვეთის სათაური',
    ],
    'filters' => [
        'assigned_user' => 'პასუხისმგებელი',
        'project' => 'პროექტი',
        'client' => 'კლიენტი',
        'status' => 'სტატუსი',
    ],
    'help' => [
        'assigned_user' => 'ვორქერი ხედავს მხოლოდ მისთვის მიბმულ შეკვეთებს.',
    ],
    'navigation' => [
        'badge' => 'შეკვეთების რაოდენობა',
        'plural' => 'შეკვეთები',
        'singular' => 'შეკვეთა',
    ],
    'sections' => [
        'details' => 'შეკვეთის დეტალები',
    ],
    'statuses' => [
        'approved' => 'დადასტურებული',
        'draft' => 'დრაფტი',
        'pending' => 'მოლოდინში',
        'rejected' => 'უარყოფილი',
    ],
];
