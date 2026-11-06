<?php
$files = glob(__DIR__ . '/../models/*.php');
foreach ($files as $file) {
    $content = file_get_contents($file);
    preg_match_all('/"(SELECT|INSERT|UPDATE|DELETE)[^"]+"/s', $content, $m);
    foreach ($m[0] as $sql) {
        preg_match_all('/:([a-zA-Z0-9_]+)/', $sql, $placeholders);
        $counts = array_count_values($placeholders[1]);
        foreach ($counts as $name => $c) {
            if ($c > 1) {
                echo "En " . basename($file) . ": :$name aparece $c veces en:\n" . trim($sql) . "\n\n";
            }
        }
    }
}

