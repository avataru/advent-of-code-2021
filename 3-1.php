<?php

$bitCounts = [];

$stream = fopen('3-input.txt', 'r');
while (($line = fgets($stream)) !== false) {
    $line = trim($line);

    for ($n = 0; $n < strlen($line); $n++) {
        // Make sure the keys are strings and prevent any magical casting to int
        $position = 'bit_' . ($n + 1);
        $bit = 'val_' . substr($line, $n, 1);

        if (!array_key_exists($position, $bitCounts)) {
            $bitCounts[$position] = [];
        }

        if (!array_key_exists($bit, $bitCounts[$position])) {
            $bitCounts[$position][$bit] = 0;
        }

        $bitCounts[$position][$bit]++;
    }
}

$gammaRate = '';
$epsilonRate = '';

foreach ($bitCounts as $bit) {
    if ($bit['val_0'] > $bit['val_1']) {
        $gammaRate .= '0';
        $epsilonRate .= '1';
    } else {
        $gammaRate .= '1';
        $epsilonRate .= '0';
    }
}

$gammaRate = bindec($gammaRate);
$epsilonRate = bindec($epsilonRate);

echo $gammaRate * $epsilonRate;
