<?php

$file = count($argv) > 1 && in_array($argv[1], ['-s', '--sample']) ? 'sample.txt' : 'input.txt';

echo sprintf("Part one: %d\n", (new PartOne($file))->solve());
echo sprintf("Part two: %d\n", (new PartTwo($file))->solve());

class PartOne
{
    protected array $fish;

    public function __construct(string $file)
    {
        $input = file_get_contents($file);
        $this->fish = explode(',', trim($input));
    }

    public function solve(): int
    {
        return (new Lanternfish($this->fish))->simulate(80)->getTotal();
    }
}

class PartTwo extends PartOne
{
    public function solve(): int
    {
        return (new Lanternfish($this->fish))->simulate(256)->getTotal();
    }
}

Class Lanternfish
{
    const SPAWNING_DURATION = 7;
    const INITIAL_CYCLE_DELAY = 2;

    public array $fish;

    public function __construct(array $fish)
    {
        for ($s = 0; $s < static::SPAWNING_DURATION + static::INITIAL_CYCLE_DELAY; $s++) {
            $this->fish['cycle_' . $s] = 0;
        }

        foreach ($fish as $spawnPosition) {
            $this->fish['cycle_' . (int) $spawnPosition]++;
        }
    }

    public function simulate(int $days): self
    {
        for ($n = 1; $n <= $days; $n++) {
            $this->cycle();
        }

        return $this;
    }

    public function getTotal(): int
    {
        return array_sum($this->fish);
    }

    private function cycle()
    {
        $fish = $this->fish;

        for ($s = 0; $s < static::SPAWNING_DURATION + static::INITIAL_CYCLE_DELAY; $s++) {
            switch ($s) {
                case (static::SPAWNING_DURATION + 1):
                    $fishInThisCycle = $fish['cycle_0'];
                    break;
                case (static::SPAWNING_DURATION - 1):
                    $fishInThisCycle = $fish['cycle_0'] + $this->fish['cycle_' . static::SPAWNING_DURATION];
                    break;
                default:
                    $fishInThisCycle = $fish['cycle_' . ($s + 1)];
            }

            $this->fish['cycle_' . $s] = $fishInThisCycle;
        }
    }
}
