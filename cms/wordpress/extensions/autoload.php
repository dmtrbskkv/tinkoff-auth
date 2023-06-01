<?php

$ext = scandir(__DIR__);

$exceptions = ['.gitkeep', 'autoload.php'];
for ($i = 2; $i < count($ext); $i++) {
    $filename = $ext[$i];
    if (in_array($filename, $exceptions)) {
        continue;
    }

    require_once __DIR__ . "/$filename";
}