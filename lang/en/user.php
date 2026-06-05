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
        'company' => 'Company',
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
    'actions' => [
        'add_existing' => [
            'label' => 'Add existing member',
            'user' => 'User',
            'role' => 'Role',
        ],
        'remove' => [
            'label' => 'Remove from company',
            'heading' => 'Remove member from company?',
            'description' => 'The user will not be deleted, only removed from the current company.',
            'submit' => 'Remove member',
            'success' => 'Member removed',
        ],
    ],
];
