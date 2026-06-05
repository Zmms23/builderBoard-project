<?php

return [
    'navigation' => [
        'singular' => 'Member',
        'plural' => 'Members',
        'badge' => 'Members in current company',
    ],
    'sections' => [
        'details' => 'Member details',
    ],
    'fields' => [
        'name' => 'Name',
        'email' => 'Email',
        'password' => 'Password',
        'role' => 'Role',
    ],
    'columns' => [
        'name' => 'Name',
        'email' => 'Email',
        'role' => 'Role',
        'created_at' => 'Created at',
    ],
    'filters' => [
        'role' => 'Role',
    ],
    'roles' => [
        'super_admin' => 'Super admin',
        'company_admin' => 'Company admin',
        'manager' => 'Manager',
        'worker' => 'Worker',
        'none' => 'No role',
    ],
    'menu' => [
        'current_role' => 'Current role: :role',
    ],
];
