<?php

declare(strict_types=1);

return [
    // Directories to search in.
    'directories' => [
        'app',
        'lang',
        'resources',
    ],

    // File Extensions to search for.
    'extensions' => [
        'php',
        'js',
    ],

    // Translation function names.
    // If your function name contains $ escape it using \$ .
    'functions' => [
        '__',
        '_t',
        '@lang',
    ],

    // Indicates weather you need to sort the translations alphabetically
    // by original strings (keys).
    // It helps navigate a translation file and detect possible duplicates.
    'sort-keys' => true,
];
