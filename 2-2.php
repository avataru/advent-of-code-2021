<?php

$position = 0;
$depth = 0;
$aim = 0;

$stream = fopen('2-input.txt', 'r');
while (($line = fgets($stream)) !== false) {
    list($direction, $amount) = explode(' ', $line);
    $amount = intval($amount);

    switch ($direction) {
        case 'forward':
            $position += $amount;
            $depth += $aim * $amount;
            break;

        case 'down':
            $aim += $amount;
            break;

        case 'up':
            $aim -= $amount;
            break;
    }
}

echo $position * $depth;
