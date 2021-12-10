<?php

$file = count($argv) > 1 && in_array($argv[1], ['-s', '--sample']) ? 'sample.txt' : 'input.txt';

echo sprintf("Part one: %d\n", (new PartOne($file))->solve());
echo sprintf("Part two: %d\n", (new PartTwo($file))->solve());

class PartOne
{
    protected array $course;

    public function __construct(string $file)
    {
        $input = file_get_contents($file);
        $this->course = explode("\n", trim($input));
    }

    public function solve(): int
    {
        return (new Navigation($this->course))->getLocation();
    }
}

class PartTwo extends PartOne
{
    public function solve(): int
    {
        return (new Navigation($this->course, true))->getLocation();
    }
}

class Navigation
{
    private bool $aimed;

    private int $position = 0;
    private int $depth = 0;
    private int $aim = 0;

    public function __construct(array $course, bool $aimed = false)
    {
        $this->aimed = $aimed;

        foreach ($course as $instruction) {
            list($direction, $amount) = explode(' ', $instruction);
            call_user_func([__CLASS__, 'go' . ucfirst($direction)], (int) $amount);
        }
    }

    public function getLocation()
    {
        return $this->position * $this->depth;
    }

    private function goForward(int $amount)
    {
        $this->position += $amount;

        if ($this->aimed) {
            $this->depth += $this->aim * $amount;
        }
    }

    private function goDown(int $amount)
    {
        if ($this->aimed) {
            $this->aim += $amount;
        } else {
            $this->depth += $amount;
        }
    }

    private function goUp(int $amount)
    {
        if ($this->aimed) {
            $this->aim -= $amount;
        } else {
            $this->depth -= $amount;
        }
    }
}
