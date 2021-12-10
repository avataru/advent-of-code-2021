<?php

$file = count($argv) > 1 && in_array($argv[1], ['-s', '--sample']) ? 'sample.txt' : 'input.txt';

echo sprintf("Part one: %d\n", (new PartOne($file))->solve());
echo sprintf("Part two: %d\n", (new PartTwo($file))->solve());

class PartOne
{
    protected DepthMap $depthMap;

    public function __construct(string $file)
    {
        $this->depthMap = new DepthMap(file_get_contents($file));
    }

    public function solve(): int
    {
        $sum = 0;
        foreach ($this->depthMap->getLowPoints() as $lowPoint) {
            $sum += $lowPoint->depth + 1;
        }

        return $sum;
    }
}

class PartTwo extends PartOne
{
    public function solve(): int
    {
        $basins = $this->depthMap->getBasins();
        usort($basins, fn(Basin $basinOne, Basin $basinTwo) => $basinTwo->getSize() <=> $basinOne->getSize());

        $product = 1;
        foreach (array_slice($basins, 0, 3) as $basin) {
            echo "{$basin->getSize()} * ";
            $product *= $basin->getSize();
        }
        echo "\n";

        return $product;
    }
}

class DepthMap
{
    const MAX_HEIGHT = 9;

    private array $map;

    public function __construct(string $data)
    {
        $this->map = array_map(fn ($line) => array_map('intval', str_split($line)), explode("\n", trim($data)));
    }

    public function getDepthMap(): array
    {
        return $this->map;
    }

    public function getDepthAt(Point $point): ?int
    {
        return $this->map[$point->x][$point->y] ?? null;
    }

    public function getLowPoints(): array
    {
        $lowPoints = [];

        foreach ($this->map as $vIndex => $line) {
            foreach ($line as $hIndex => $depth) {
                if ($depth < ($this->getDepthAt(Point::at($vIndex, $hIndex + 1)) ?? static::MAX_HEIGHT) &&
                    $depth < ($this->getDepthAt(Point::at($vIndex, $hIndex - 1)) ?? static::MAX_HEIGHT) &&
                    $depth < ($this->getDepthAt(Point::at($vIndex + 1, $hIndex)) ?? static::MAX_HEIGHT) &&
                    $depth < ($this->getDepthAt(Point::at($vIndex - 1, $hIndex)) ?? static::MAX_HEIGHT)
                ){
                    $lowPoint = Point::at($vIndex, $hIndex);
                    $lowPoint->setDepth($this->getDepthAt($lowPoint));
                    array_push($lowPoints, $lowPoint);
                }
            }
        }

        return $lowPoints;
    }

    public function getBasins(): array
    {
        $basins = [];

        foreach ($this->getLowPoints() as $point) {
            $toCheck = [$point];
            $checked = [];
            $basin = new Basin();

            while (count($toCheck) > 0) {
                $point = array_shift($toCheck);

                if (in_array((string) $point, $checked)) {
                    continue;
                }

                array_push($checked, (string) $point);

                if ($point->depth < static::MAX_HEIGHT) {
                    $basin->addPoint($point);

                    $north = $point->getNorth();
                    if (!is_null($this->getDepthAt($north))) {
                        $north->setDepth($this->getDepthAt($north));
                        array_push($toCheck, $north);
                    }

                    $east = $point->getEast();
                    if (!is_null($this->getDepthAt($east))) {
                        $east->setDepth($this->getDepthAt($east));
                        array_push($toCheck, $east);
                    }

                    $south = $point->getSouth();
                    if (!is_null($this->getDepthAt($south))) {
                        $south->setDepth($this->getDepthAt($south));
                        array_push($toCheck, $south);
                    }

                    $west = $point->getWest();
                    if (!is_null($this->getDepthAt($west))) {
                        $west->setDepth($this->getDepthAt($west));
                        array_push($toCheck, $west);
                    }
                }
            }

            array_push($basins, $basin);
        }

        return $basins;
    }
}

class Point
{
    public int $x;
    public int $y;
    public int $depth;

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

    public function setDepth(int $depth)
    {
        $this->depth = $depth;
    }

    public function getNorth(): self
    {
        return self::at($this->x - 1, $this->y);
    }

    public function getEast(): self
    {
        return self::at($this->x, $this->y + 1);
    }

    public function getSouth(): self
    {
        return self::at($this->x + 1, $this->y);
    }

    public function getWest(): self
    {
        return self::at($this->x, $this->y - 1);
    }

}

class Basin
{
    public array $points = [];

    public function addPoint(Point $point)
    {
        array_push($this->points, $point);
    }

    public function getSize()
    {
        return count($this->points);
    }
}
