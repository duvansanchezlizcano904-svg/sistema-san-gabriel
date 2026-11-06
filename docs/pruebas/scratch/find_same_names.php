<?php
$dir = __DIR__ . '/../';
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS));

$names = [];

foreach ($it as $file) {
    if ($file->isDir()) continue;
    $path = str_replace('\\', '/', $file->getPathname());
    if (strpos($path, '/.git/') !== false || strpos($path, '/scratch/') !== false) continue;
    
    $name = basename($path);
    $names[$name][] = str_replace(str_replace('\\', '/', realpath($dir)) . '/', '', $path);
}

echo "=== ARCHIVOS CON EL MISMO NOMBRE ===\n";
foreach ($names as $name => $files) {
    if (count($files) > 1) {
        echo "Nombre: $name\n";
        foreach ($files as $f) {
            echo "  - $f\n";
        }
        echo "\n";
    }
}

