<?php

$increases = 0;
$previousDepth = null;

$stream = fopen('1-input.txt', 'r');
while (($line = fgets($stream)) !== false) {
    $currentDepth = (int) $line;

    if (!is_null($previousDepth) && $currentDepth > $previousDepth) {
        $increases++;
    }

    $previousDepth = $currentDepth;
}

echo $increases;
