<?php
$baseDir = dirname(__DIR__);
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir, FilesystemIterator::SKIP_DOTS));

$errors = [];
$checked = 0;

foreach ($it as $file) {
    if (!$file->isFile()) continue;
    $path = str_replace('\\', '/', $file->getPathname());
    if (pathinfo($path, PATHINFO_EXTENSION) !== 'php') continue;
    if (strpos($path, '/.git/') !== false || strpos($path, '/scratch/') !== false) continue;
    
    $checked++;
    $output = [];
    $returnVar = 0;
    exec('php -l "' . $path . '" 2>&1', $output, $returnVar);
    if ($returnVar !== 0) {
        $errors[] = [
            'file' => $path,
            'output' => implode("\n", $output)
        ];
    }
}

echo "Checked $checked PHP files.\n";
if (empty($errors)) {
    echo "ALL PHP FILES HAVE VALID SYNTAX! (0 syntax errors)\n";
} else {
    echo "Found " . count($errors) . " syntax errors:\n";
    foreach ($errors as $e) {
        echo "File: " . $e['file'] . "\n" . $e['output'] . "\n\n";
    }
}
