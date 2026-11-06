<?php
$files = glob(__DIR__ . '/../docs/*.md');
$files = array_merge($files, glob(__DIR__ . '/../docs/pruebas/*.md'));

$brokenPaths = [];

foreach ($files as $mdFile) {
    $content = file_get_contents($mdFile);
    $relName = basename($mdFile);
    // Find backtick paths or markdown links like `models/Producto.php`, `controllers/...`, `views/...`
    preg_match_all('/`([a-zA-Z0-9_\-\.\/]+\.(?:php|sql|css|js|html|md))`/', $content, $m);
    foreach ($m[1] as $path) {
        // Normalize path
        $cleanPath = ltrim($path, './');
        $fullPath = __DIR__ . '/../' . $cleanPath;
        if (!file_exists($fullPath) && !file_exists(__DIR__ . '/../docs/' . $cleanPath) && !file_exists(__DIR__ . '/../database/' . $cleanPath) && !file_exists(__DIR__ . '/../database/migrations/' . $cleanPath)) {
            $brokenPaths[] = [
                'doc' => $relName,
                'path' => $path
            ];
        }
    }
}

echo "Broken paths found: " . count($brokenPaths) . "\n";
foreach (array_slice($brokenPaths, 0, 20) as $bp) {
    echo "Doc: {$bp['doc']} -> Path: {$bp['path']}\n";
}

