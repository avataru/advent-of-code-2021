<?php

require __DIR__ . '/../Provisioning/vendor/autoload.php';

$file = count($argv) > 1 && in_array($argv[1], ['-s', '--sample']) ? 'sample.txt' : 'input.txt';

echo sprintf("Part one: %d\n", (new PartOne($file))->solve());
echo sprintf("Part two: %d\n", (new PartTwo($file))->solve());

class PartOne
{
    protected Cave $cave;

    public function __construct(string $file)
    {
        $input = file_get_contents(__DIR__ . '/' . $file);
        $data = array_map(fn ($line) => array_map('intval', str_split($line)), explode("\n", trim($input)));
        $this->cave = new Cave($data);
    }

    public function solve(): int
    {
        return $this->cave->getSafestPath();
    }
}

class PartTwo extends PartOne
{
    public function solve(): int
    {
        return $this->cave->expand(5)->getSafestPath();
    }
}

class Cave
{
    public array $data;
    public int $width = 0;
    public int $height = 0;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->width = count($this->data[0]);
        $this->height = count($this->data);
    }

    public function expand(int $factor): self
    {
        $newData = [];

        for ($v = 0; $v < $this->height * $factor; $v++) {
            for ($h = 0; $h < $this->width * $factor; $h++) {
                $riskFactor = ($v - $v % 10) / 10 + ($h - $h % 10) / 10;
                $risk = $this->data[$v % 10][$h % 10] + $riskFactor;
                if ($risk > 9) {
                    $risk -= 9;
                }

                $newData[$v][$h] = $risk;
            }
        }

        return new Cave($newData);
    }

    public function print(array $path)
    {
        foreach ($this->data as $v => $line) {
            foreach ($line as $h => $risk) {
                if (in_array(static::name([$v, $h]), $path)) {
                    echo "\e[32m{$risk}";
                } else {
                    echo "\e[37m{$risk}";
                }
            }

            echo "\n";
        }
    }

    public function getSafestPath(): int
    {
        $queue = new SplPriorityQueue;
        $start = [0, 0];
        $visited = [static::name($start)];
        $queue->insert([0, $start], 0);

        while (!$queue->isEmpty()) {
            [$risk, [$y, $x]] = $queue->extract();

            if ($y === $this->height - 1 && $x === $this->width - 1) {
                // $this->print($visited);
                return $risk;
            }

            foreach ($this->getNeighbours($y, $x) as [$neighbourY, $neighbourX]) {
                $neighbourKey = static::name([$neighbourY, $neighbourX]);
                if (!in_array($neighbourKey, $visited)) {
                    $newRisk = $risk + $this->data[$neighbourY][$neighbourX];
                    $queue->insert([$newRisk, [$neighbourY, $neighbourX]], 0 - $newRisk);
                    $visited[] = $neighbourKey;
                }
            }
        }
    }

    private function getNeighbours(int $y, int $x): array
    {
        $neighbours = [];

        foreach ([[-1, 0], [1, 0], [0, -1], [0, 1]] as [$deltaY, $deltaX]) {
            $neighbourY = $y + $deltaY;
            $neighbourX = $x + $deltaX;

            if ($neighbourY >= 0 && $neighbourY < $this->height && $neighbourX >= 0 && $neighbourX < $this->width) {
                $neighbours[] = [$neighbourY, $neighbourX];
            }
        }

        return $neighbours;
    }

    private static function name(array $node): string
    {
        return implode('x', $node);
    }
}
