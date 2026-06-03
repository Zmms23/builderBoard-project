<?php

return [
    'columns' => [
        'budget' => 'ბიუჯეტი',
        'client' => 'კლიენტი',
        'deadline' => 'დედლაინი',
        'order' => 'შეკვეთა',
        'progress' => 'პროგრესი',
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
    'tasks' => [
        'columns' => [
            'assignee' => 'Responsible',
            'budget' => 'Budget',
            'deadline' => 'Deadline',
            'name' => 'Task',
            'notes' => 'Notes',
            'sort' => 'Order',
            'status' => 'Status',
        ],
        'fields' => [
            'assignee' => 'Responsible',
            'budget' => 'Budget',
            'deadline' => 'Deadline',
            'name' => 'Task name',
            'notes' => 'Notes',
            'sort' => 'Order',
            'status' => 'Status',
        ],
        'filters' => [
            'status' => 'Status',
        ],
    ],
    'task_statuses' => [
        'blocked' => 'Blocked',
        'done' => 'Done',
        'in_progress' => 'In progress',
        'todo' => 'To do',
    ],
];
