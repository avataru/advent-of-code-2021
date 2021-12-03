<?php

$data = [];
$stream = fopen('3-input.txt', 'r');
while (($line = fgets($stream)) !== false) {
    $data[] = trim($line);
}
fclose($stream);

echo getOxigenGeneratorRating($data) * getCO2ScrubberRating($data);

function getMostCommonBit(int $position, array $data)
{
    $bitCounts = getBitCounts($position, $data);

    // echo sprintf("The most common bit in position %d is %d because %s\n", $position + 1, $bitCounts['zeroes'] <= $bitCounts['ones'] ? '1' : '0', json_encode($bit));

    return $bitCounts['zeroes'] <= $bitCounts['ones'] ? '1' : '0';
}

function getLeastCommonBit(int $position, array $data)
{
    $bitCounts = getBitCounts($position, $data);

    // echo sprintf("The least common bit in position %d is %d because %s\n", $position + 1, $bitCounts['zeroes'] <= $bitCounts['ones'] ? '1' : '0', json_encode($bit));

    return $bitCounts['zeroes'] <= $bitCounts['ones'] ? '0' : '1';
}

function getBitCounts(int $position, array $data)
{
    $zeroes = 0;
    $ones = 0;

    foreach ($data as $line) {
        $bit = substr($line, $position, 1);
        if ($bit === '0') {
            $zeroes++;
        } else {
            $ones++;
        }
    }

    return compact('zeroes', 'ones');
}

function getOxigenGeneratorRating(array $data): int
{
    $filteredData = $data;

    for ($n = 0; $n < strlen($data[0]); $n++) {
        $filteredData = array_filter($filteredData, function($line) use ($n, $filteredData) {
            return substr($line, $n, 1) === getMostCommonBit($n, $filteredData);
        });

        // echo sprintf("Position %d: %s\n", $n + 1, json_encode($filteredData));

        if (count($filteredData) === 1) {
            return bindec(reset($filteredData));
        }
    }
}

function getCO2ScrubberRating(array $data): int
{
    $filteredData = $data;

    for ($n = 0; $n < strlen($data[0]); $n++) {
        $filteredData = array_filter($filteredData, function($line) use ($n, $filteredData) {
            return substr($line, $n, 1) === getLeastCommonBit($n, $filteredData);
        });

        // echo sprintf("Position %d: %s\n", $n + 1, json_encode($filteredData));

        if (count($filteredData) === 1) {
            return bindec(reset($filteredData));
        }
    }
}
