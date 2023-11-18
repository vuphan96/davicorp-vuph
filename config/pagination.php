<?php
return
    [
        'admin' => [
            'big' => env('BIG_PAGINATION', 50),
            'medium' => env('MEDIUM_PAGINATION', 20),
            'small' => env('SMALL_PAGINATION', 10)
        ],
        'user' => [

        ],
        'search' => [
            'default' => env('SEARCH_LIMIT_DEFAULT', 20),
        ],
        'notification' => 10
    ];