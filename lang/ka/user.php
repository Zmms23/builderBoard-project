<?php

return [
    'navigation' => [
        'singular' => 'წევრი',
        'plural' => 'წევრები',
        'badge' => 'მიმდინარე კომპანიის წევრები',
    ],
    'sections' => [
        'details' => 'წევრის დეტალები',
    ],
    'fields' => [
        'name' => 'სახელი',
        'email' => 'იმეილი',
        'password' => 'პაროლი',
        'role' => 'როლი',
    ],
    'columns' => [
        'name' => 'სახელი',
        'email' => 'იმეილი',
        'role' => 'როლი',
        'created_at' => 'შექმნის თარიღი',
    ],
    'filters' => [
        'role' => 'როლი',
    ],
    'roles' => [
        'super_admin' => 'სუპერ ადმინი',
        'company_admin' => 'კომპანიის ადმინი',
        'manager' => 'მენეჯერი',
        'worker' => 'ვორქერი',
        'none' => 'როლი არ აქვს',
    ],
    'menu' => [
        'current_role' => 'მიმდინარე როლი: :role',
    ],
];
