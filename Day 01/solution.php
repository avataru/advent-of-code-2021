<?php

$file = count($argv) > 1 && in_array($argv[1], ['-s', '--sample']) ? 'sample.txt' : 'input.txt';

echo sprintf("Part one: %d\n", (new PartOne($file))->solve());
echo sprintf("Part two: %d\n", (new PartTwo($file))->solve());

class PartOne
{
    protected array $measurements;

    public function __construct(string $file)
    {
        $input = file_get_contents($file);
        $this->measurements = explode("\n", trim($input));
    }

    public function solve(): int
    {
        return (new Sonar($this->measurements))->getIncreases();
    }
}

class PartTwo extends PartOne
{
    public function solve(): int
    {
        return (new Sonar($this->measurements))->getIncreases(3);
    }
}

class Sonar
{
    private $measurements = [];

    public function __construct(array $measurements)
    {
        $this->measurements = $measurements;
    }

    public function getIncreases(int $size = 1)
    {
        $increases = 0;
        for ($i = $size + 1; $i <= count($this->measurements); $i++) {
            $previousValue = array_sum(array_slice($this->measurements, $i - $size - 1, $size));
            $currentValue = array_sum(array_slice($this->measurements, $i - $size, $size));

            if ($previousValue < $currentValue) {
                $increases++;
            }
        }

        return $increases;
    }
}
