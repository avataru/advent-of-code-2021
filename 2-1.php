<?php

$position = 0;
$depth = 0;

$stream = fopen('2-input.txt', 'r');
while (($line = fgets($stream)) !== false) {
    list($direction, $amount) = explode(' ', $line);

    switch ($direction) {
        case 'forward':
            $position += intval($amount);
            break;

        case 'down':
            $depth += intval($amount);
            break;

        case 'up':
            $depth -= intval($amount);
            break;
    }
}

echo $position * $depth;
