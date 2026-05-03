<?php

return [
    'name' => 'AIBot',

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY', ''),
        'model' => env('GEMINI_MODEL', 'gemini-2.0-flash'),
        'timeout' => (int) env('GEMINI_TIMEOUT', 60),
        'max_tool_rounds' => (int) env('GEMINI_MAX_TOOL_ROUNDS', 16),
    ],
];
