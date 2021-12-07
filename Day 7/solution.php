<?php

$file = count($argv) > 1 && in_array($argv[1], ['-s', '--sample']) ? 'sample.txt' : 'input.txt';

echo sprintf("Part one: %d\n", (new PartOne($file))->solve());
echo sprintf("Part two: %d\n", (new PartTwo($file))->solve());

class PartOne
{
    protected array $positions;

    public function __construct(string $file)
    {
        $input = file_get_contents($file);
        $this->positions = array_map(fn($value) => (int) $value, explode(',', trim($input)));
    }

    public function solve(): int
    {
        $median = static::median($this->positions);

        $fuel = 0;
        foreach ($this->positions as $position) {
            $fuel += abs($position - $median);
        }

        return $fuel;
    }

    private static function median(array $values): int
    {
        sort($values);

        $middle = count($values) / 2;
        if ($middle % 2 === 0) {
            return $values[floor($middle)];
        } else {
            return round(($values[$middle - 1] + $values[$middle]) / 2);
        }
    }
}

class PartTwo extends PartOne
{
    public function solve(): int
    {
        $averages = static::averages($this->positions);

        $fuel = [];
        foreach ($averages as $average) {
            $value = 0;
            foreach ($this->positions as $position) {
                $distance = abs($position - $average);
                $value += ($distance * $distance + $distance) / 2;
            }
            array_push($fuel, $value);
        }

        return min($fuel);
    }

    private static function averages(array $values): array
    {
        $average = array_sum($values) / count($values);

        return [floor($average), ceil($average)];
    }
}
