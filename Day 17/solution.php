<?php

require __DIR__ . '/../Provisioning/vendor/autoload.php';

$file = count($argv) > 1 && in_array($argv[1], ['-s', '--sample']) ? 'sample.txt' : 'input.txt';

echo sprintf("Part one: %d\n", (new PartOne($file))->solve());
echo sprintf("Part two: %d\n", (new PartTwo($file))->solve());

class PartOne
{
    protected Probe $probe;

    public function __construct(string $file)
    {
        $input = file_get_contents(__DIR__ . '/' . $file);
        [$minX, $maxX, $minY, $maxY] = sscanf($input, 'target area: x=%d..%d, y=%d..%d');
        $this->probe = new Probe([$minX, $maxX], [$minY, $maxY]);
    }

    public function solve(): int
    {
        $maxHeight = 0;

        for ($x = 0; $x <= max($this->probe->targetX); $x++) {
            for ($y = 0; $y <= max($this->probe->targetX); $y++) {
                if ($this->probe->launch($x, $y)) {
                    $maxHeight = max([$this->probe->getMaxHeight(), $maxHeight]);
                }
            }
        }

        return $maxHeight;
    }
}

class PartTwo extends PartOne
{
    public function solve(): int
    {
        $launches = 0;
        $l = [];

        for ($x = 0; $x <= max($this->probe->targetX); $x++) {
            for ($y = min($this->probe->targetY); $y <= abs(min($this->probe->targetY)); $y++) {
                if ($this->probe->launch($x, $y)) {
                    $launches++;
                    $l[] = [$x, $y];
                }
            }
        }

        return $launches;
    }
}

class Probe
{
    public array $targetX, $targetY;
    public array $trajectory;

    public function __construct(array $targetX, array $targetY)
    {
        $this->targetX = $targetX;
        $this->targetY = $targetY;
    }

    public function launch(int $x, int $y): bool
    {
        $currentX = 0;
        $currentY = 0;
        $this->trajectory = [];

        while ($currentX <= max($this->targetX) && $currentY >= min($this->targetY)) {
            $currentX += $x;
            $currentY += $y;

            $this->trajectory[] = [$currentX, $currentY];

            if ($currentX >= min($this->targetX) && $currentX <= max($this->targetX) && $currentY <= max($this->targetY) && $currentY >= min($this->targetY)) {
                return true;
            }

            if ($x > 0) { $x--; }
            elseif ($x < 0) { $x++; }
            $y--;
        }

        return false;
    }

    public function getMaxHeight(): int
    {
        $heights = [];

        foreach ($this->trajectory as $point) {
            $heights[] = $point[1];
        }

        return max($heights);
    }
}
