<?php

return [
    'url' => env('GITLAB_REPORT_URL'),
    'token' => env('GITLAB_REPORT_TOKEN'),
    'project_id' => env('GITLAB_REPORT_PROJECT_ID'),
    'labels' => env('GITLAB_REPORT_LABELS',''),
    'ignore-exceptions' => [
        \Symfony\Component\Console\Exception\CommandNotFoundException::class,
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Validation\ValidationException::class,
    ],
    'redacted-fields' => [
        'password',
        'password_confirmation'
    ]
];