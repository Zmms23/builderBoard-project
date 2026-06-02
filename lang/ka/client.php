<?php

return [
    'columns' => [
        'address' => 'მისამართი',
        'email' => 'ელ. ფოსტა',
        'name' => 'სახელი',
        'notes' => 'შენიშვნები',
        'phone' => 'ტელეფონი',
        'status' => 'სტატუსი',
        'type' => 'ტიპი',
        'updated_at' => 'განახლდა',
    ],
    'fields' => [
        'address' => 'მისამართი',
        'email' => 'ელ. ფოსტა',
        'name' => 'კლიენტის სახელი',
        'notes' => 'შენიშვნები',
        'phone' => 'ტელეფონი',
        'status' => 'სტატუსი',
        'type' => 'კლიენტის ტიპი',
    ],
    'filters' => [
        'status' => 'სტატუსი',
        'type' => 'კლიენტის ტიპი',
    ],
    'navigation' => [
        'badge' => 'კლიენტების რაოდენობა',
        'plural' => 'კლიენტები',
        'singular' => 'კლიენტი',
    ],
    'sections' => [
        'details' => 'კლიენტის დეტალები',
    ],
    'statuses' => [
        'active' => 'აქტიური',
        'inactive' => 'არააქტიური',
        'lead' => 'ლიდი',
    ],
    'types' => [
        'company' => 'კომპანია',
        'person' => 'ფიზიკური პირი',
    ],
];
