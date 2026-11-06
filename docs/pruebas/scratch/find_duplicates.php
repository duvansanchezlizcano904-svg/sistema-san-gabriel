<?php
$dir = __DIR__ . '/../';
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS));

$hashes = [];

foreach ($it as $file) {
    if ($file->isDir()) continue;
    $path = str_replace('\\', '/', $file->getPathname());
    if (strpos($path, '/.git/') !== false || strpos($path, '/scratch/') !== false) continue;
    
    $hash = md5_file($path);
    $hashes[$hash][] = str_replace(str_replace('\\', '/', realpath($dir)) . '/', '', $path);
}

echo "=== ARCHIVOS CON CONTENIDO EXACTAMENTE IDÉNTICO ===\n";
foreach ($hashes as $hash => $files) {
    if (count($files) > 1) {
        echo "Hash: $hash\n";
        foreach ($files as $f) {
            echo "  - $f\n";
        }
        echo "\n";
    }
}

