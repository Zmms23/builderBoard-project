<?php

return [
    'columns' => [
        'amount' => 'თანხა',
        'client' => 'კლიენტი',
        'created_at' => 'შეიქმნა',
        'method' => 'მეთოდი',
        'order' => 'შეკვეთა',
        'paid_at' => 'გადახდის თარიღი',
        'project' => 'პროექტი',
        'reference' => 'რეფერენსი',
        'status' => 'სტატუსი',
    ],
    'fields' => [
        'amount' => 'თანხა',
        'client' => 'კლიენტი',
        'method' => 'გადახდის მეთოდი',
        'notes' => 'შენიშვნები',
        'order' => 'შეკვეთა',
        'paid_at' => 'გადახდის თარიღი',
        'project' => 'პროექტი',
        'reference' => 'რეფერენსი',
        'status' => 'სტატუსი',
    ],
    'filters' => [
        'client' => 'კლიენტი',
        'method' => 'მეთოდი',
        'order' => 'შეკვეთა',
        'status' => 'სტატუსი',
    ],
    'methods' => [
        'bank_transfer' => 'ბანკით ჩარიცხვა',
        'card' => 'ბარათი',
        'cash' => 'ქეში',
        'other' => 'სხვა',
    ],
    'navigation' => [
        'badge' => 'გადახდების რაოდენობა',
        'plural' => 'გადახდები',
        'singular' => 'გადახდა',
    ],
    'sections' => [
        'details' => 'გადახდის დეტალები',
    ],
    'statuses' => [
        'failed' => 'წარუმატებელი',
        'paid' => 'გადახდილი',
        'pending' => 'მოლოდინში',
        'refunded' => 'დაბრუნებული',
    ],
];
