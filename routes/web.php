<?php

// This file holds the routes for the application.
// The router will load this file to know which controller to call.

return [
    'GET' => [
        '' => 'DashboardController@index',
        'dashboard' => 'DashboardController@index',
        'register' => 'AuthController@create',
        'login' => 'AuthController@login',
        'download' => 'DocumentController@download'
    ],
    'POST' => [
        'register' => 'AuthController@store',
        'login' => 'AuthController@authenticate',
        'logout' => 'AuthController@logout',
        'documents' => 'DocumentController@store'
    ]
];
