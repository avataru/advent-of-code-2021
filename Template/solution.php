<?php

$file = count($argv) > 1 && in_array($argv[1], ['-s', '--sample']) ? 'sample.txt' : 'input.txt';

echo sprintf("Part one: %d\n", (new PartOne($file))->solve());
echo sprintf("Part two: %d\n", (new PartTwo($file))->solve());

class PartOne
{
    public function __construct(string $file)
    {
        $input = file_get_contents($file);
        // TO DO
    }

    public function solve(): int
    {
        // TO DO
    }
}

class PartTwo extends PartOne
{
    public function solve(): int
    {
        // TO DO
    }
}

function d(...$data)
{
    foreach ($data as $value) {
        var_dump($value) . "\n";
    }
}

function dd(...$data)
{
    foreach ($data as $value) {
        var_dump($value) . "\n";
    }

    die();
}
