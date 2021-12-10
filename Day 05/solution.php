<?php

$file = count($argv) > 1 && in_array($argv[1], ['-s', '--sample']) ? 'sample.txt' : 'input.txt';

echo sprintf("Part one: %d\n", (new PartOne($file))->solve());
echo sprintf("Part two: %d\n", (new PartTwo($file))->solve());

class PartOne
{
    protected array $data;

    public function __construct(string $file)
    {
        $input = file_get_contents($file);
        $this->data = explode("\n", trim($input));
    }

    public function solve(): int
    {
        return (new VentMap($this->data, true))->countIntersections();
    }
}

class PartTwo extends PartOne
{
    public function solve(): int
    {
        return (new VentMap($this->data, false))->countIntersections();
    }
}

class VentMap
{
    public array $map;

    public function __construct(array $data, bool $simple = false)
    {
        $this->generateMap($data, $simple);
    }

    public function countIntersections(): int
    {
        $intersections = 0;
        foreach ($this->map as $column) {
            foreach ($column as $point) {
                if ($point > 1) {
                    $intersections++;
                }
            }
        }

        return $intersections;
    }

    private function generateMap(array $data, bool $simple = false)
    {
        $this->map = [];

        foreach ($data as $entry) {
            $foo = preg_match('/^(\d+),(\d+)\s+->\s+(\d+),(\d+)$/', $entry, $matches);
            list(, $startX, $startY, $endX, $endY) = array_map(fn($match) => (int) $match, $matches);

            if ($simple && $startX !== $endX && $startY !== $endY) {
                continue;
            }

            foreach (static::getLinePoints([$startX, $startY], [$endX, $endY]) as $point) {
                if (!array_key_exists($point[0], $this->map)) {
                    $this->map[$point[0]] = [];
                }

                if (!array_key_exists($point[1], $this->map[$point[0]])) {
                    $this->map[$point[0]][$point[1]] = 0;
                }

                $this->map[$point[0]][$point[1]]++;
            }
        }
    }

    private static function getLinePoints(array $startPoint, array $endPoint)
    {
        $points = [];

        $steps = max(abs($startPoint[0] - $endPoint[0]), abs($startPoint[1] - $endPoint[1]));
        if ($steps == 0) {
            return $points;
        }

        $xStep = ($endPoint[0] - $startPoint[0] ) / $steps;
        $yStep = ($endPoint[1] - $startPoint[1] ) / $steps;

        $points[] = $currentPoint = $startPoint;

        for ($step = 0; $step < $steps; $step++) {
            $currentPoint[0] += $xStep;
            $currentPoint[1] += $yStep;
            array_push($points, [(int) round($currentPoint[0], 0), (int) round($currentPoint[1], 0)]);
        }

        return $points;
    }
}
