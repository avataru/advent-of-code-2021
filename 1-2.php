<?php

$increases = 0;
$previousWindow = [null, null, null];
$currentWindow = [null, null, null];

$stream = fopen('1-input.txt', 'r');
while (($line = fgets($stream)) !== false) {
    $currentDepth = (int) $line;

    $currentWindow = updateWindow($previousWindow, $currentDepth);

    if (!in_array(null, $previousWindow)) {
        if (array_sum($currentWindow) > array_sum($previousWindow)) {
            $increases++;
        }
    }

    $previousWindow = $currentWindow;
}

echo $increases;

function updateWindow(array $window, int $depth): array
{
    array_shift($window);
    array_push($window, $depth);

    return $window;
}
