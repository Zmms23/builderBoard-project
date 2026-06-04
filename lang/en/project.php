<?php

return [
    'columns' => [
        'budget' => 'Budget',
        'client' => 'Client',
        'deadline' => 'Deadline',
        'order' => 'Order',
        'progress' => 'Progress',
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
