<?php

return [
    'actions' => [
        'complete' => [
            'label' => 'დასრულება',
            'success' => 'პროექტი დასრულდა',
        ],
        'open_workspace' => [
            'label' => 'სამუშაო სივრცე',
        ],
        'start' => [
            'label' => 'დაწყება',
            'success' => 'პროექტი დაიწყო',
        ],
    ],
    'columns' => [
        'budget' => 'ბიუჯეტი',
        'client' => 'კლიენტი',
        'deadline' => 'დედლაინი',
        'order' => 'შეკვეთა',
        'orders_count' => 'შეკვეთები',
        'paid' => 'გადახდილი',
        'proof_uploads_count' => 'ფოტოები',
        'progress' => 'პროგრესი',
        'remaining' => 'დარჩენილი',
        'status' => 'სტატუსი',
        'title' => 'სათაური',
        'updated_at' => 'განახლდა',
    ],
    'fields' => [
        'budget' => 'ბიუჯეტი',
        'client' => 'კლიენტი',
        'deadline' => 'დედლაინი',
        'notes' => 'შენიშვნები',
        'order' => 'დადასტურებული შეკვეთა',
        'progress' => 'პროგრესი',
        'status' => 'სტატუსი',
        'title' => 'პროექტის სათაური',
    ],
    'filters' => [
        'status' => 'სტატუსი',
    ],
    'navigation' => [
        'badge' => 'პროექტების რაოდენობა',
        'plural' => 'პროექტები',
        'singular' => 'პროექტი',
    ],
    'sections' => [
        'details' => 'პროექტის დეტალები',
    ],
    'statuses' => [
        'active' => 'აქტიური',
        'canceled' => 'გაუქმებული',
        'completed' => 'დასრულებული',
        'on_hold' => 'შეჩერებული',
        'planning' => 'დაგეგმვა',
    ],
    'timeline' => [
        'columns' => [
            'ends_at' => 'დასრულება',
            'name' => 'ეტაპი',
            'notes' => 'შენიშვნები',
            'sort' => 'რიგი',
            'starts_at' => 'დაწყება',
            'status' => 'სტატუსი',
        ],
        'fields' => [
            'ends_at' => 'დასრულება',
            'name' => 'ეტაპის სახელი',
            'notes' => 'შენიშვნები',
            'sort' => 'რიგი',
            'starts_at' => 'დაწყება',
            'status' => 'სტატუსი',
        ],
        'filters' => [
            'status' => 'სტატუსი',
        ],
    ],
    'timeline_stage_statuses' => [
        'blocked' => 'დაბლოკილი',
        'completed' => 'დასრულებული',
        'in_progress' => 'მიმდინარეობს',
        'pending' => 'მოლოდინში',
    ],
];
