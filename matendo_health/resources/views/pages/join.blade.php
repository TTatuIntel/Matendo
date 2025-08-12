@php
$content = file_get_contents(resource_path('html/join.html'));

// Add CSRF token
$content = str_replace(
    '<meta name="csrf-token" content="{{ csrf_token() }}">',
    '<meta name="csrf-token" content="' . csrf_token() . '">',
    $content
);

// Replace routes and image paths
$content = preg_replace_callback(
    [
        '/href="(index|about|join|hire|careers)\.html"/',
        '/src="(\.\/)?(images\/[^"]+)"/',
    ],
    function ($matches) {
        if (isset($matches[2])) { // Image path match
            return 'src="' . asset($matches[2]) . '"';
        }
        // Route match
        $routes = [
            'index' => 'home',
            'about' => 'about',
            'join' => 'join',
            'hire' => 'hire',
            'careers' => 'careers'
        ];
        return 'href="' . route($routes[$matches[1]]) . '"';
    },
    $content
);

echo $content;
@endphp
