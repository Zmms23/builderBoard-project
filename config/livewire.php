<?php

return [
    'temporary_file_upload' => [
        'disk' => 'public',
        'directory' => 'livewire-tmp',
        'rules' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
        'middleware' => null,
        'preview_mimes' => [
            'png',
            'jpg',
            'jpeg',
            'webp',
            'svg',
        ],
        'max_upload_time' => 5,
        'cleanup' => true,
    ],
];
