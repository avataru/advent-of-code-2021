<?php

$file = count($argv) > 1 && in_array($argv[1], ['-s', '--sample']) ? 'sample.txt' : 'input.txt';

echo sprintf("Part one: %d\n", (new PartOne($file))->solve());
echo sprintf("Part two: %d\n", (new PartTwo($file))->solve());

class PartOne
{
    protected OctoGrid $octoGrid;

    public function __construct(string $file)
    {
        $this->octoGrid = new OctoGrid(file_get_contents($file));
    }

    public function solve(): int
    {
        for ($n = 1; $n <= 100; $n++) {
            $this->octoGrid->step();
        }

        return $this->octoGrid->flashes;
    }
}

class PartTwo extends PartOne
{
    public function solve(): int
    {
        while (count($this->octoGrid->flashedThisStep) < $this->octoGrid->size) {
            $this->octoGrid->step();
        }

        return $this->octoGrid->steps;
    }
}

class OctoGrid
{
    protected array $grid;
    public int $size;
    public int $steps = 0;
    public int $flashes = 0;
    public array $flashedThisStep = [];

    public function __construct(string $data)
    {
        $this->grid = array_map(fn ($line) => array_map('intval', str_split($line)), explode("\n", trim($data)));
        $this->size = count($this->grid) * count($this->grid[0]);
    }

    public function step(): int
    {
        $this->flashedThisStep = [];
        $this->steps++;

        foreach ($this->grid as $y => $row) {
            foreach ($row as $x => $energy) {
                $this->energize(Octopus::at($x, $y));
            }
        }

        return $this->flashes;
    }

    public function print()
    {
        foreach ($this->grid as $row) {
            echo implode('', $row) . "\n";
        }
        echo "\n";
    }

    protected function energize(Octopus $octopus): bool
    {
        if (array_key_exists((string) $octopus, $this->flashedThisStep)) {
            return false;
        }

        if (!array_key_exists($octopus->y, $this->grid) || !array_key_exists($octopus->x, $this->grid[$octopus->y])) {
            return false;
        }

        if ($this->grid[$octopus->y][$octopus->x] === 9) {
            $this->flash($octopus);

            return true;
        }

        $this->grid[$octopus->y][$octopus->x]++;

        return false;
    }

    protected function flash(Octopus $octopus)
    {
        $this->flashes++;
        $this->flashedThisStep[(string) $octopus] = $octopus;
        $this->grid[$octopus->y][$octopus->x] = 0;

        $this->energize($octopus->north()->west());
        $this->energize($octopus->north());
        $this->energize($octopus->north()->east());
        $this->energize($octopus->east());
        $this->energize($octopus->south()->east());
        $this->energize($octopus->south());
        $this->energize($octopus->south()->west());
        $this->energize($octopus->west());
    }
}

class Octopus
{
    public int $x;
    public int $y;

    public static function at(int $x, int $y): self
    {
        return new self($x, $y);
    }

    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function __toString(): string
    {
        return $this->x . 'x' . $this->y;
    }

    public function north(): self
    {
        return Octopus::at($this->x, $this->y - 1);
    }

    public function east(): self
    {
        return Octopus::at($this->x + 1, $this->y);
    }

    public function south(): self
    {
        return Octopus::at($this->x, $this->y + 1);
    }

    public function west(): self
    {
        return Octopus::at($this->x - 1, $this->y);
    }
}
