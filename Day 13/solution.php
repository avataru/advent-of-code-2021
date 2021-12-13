<?php

require __DIR__ . '/../Provisioning/vendor/autoload.php';

$file = count($argv) > 1 && in_array($argv[1], ['-s', '--sample']) ? 'sample.txt' : 'input.txt';

echo sprintf("Part one: %d\n", (new PartOne($file))->solve());
echo sprintf("Part two: \n%s\n", (new PartTwo($file))->solve());

class PartOne
{
    protected array $points;
    protected array $folds;

    public function __construct(string $file)
    {
        $input = file_get_contents(__DIR__ . '/' . $file);
        list($points, $folds) = explode("\n\n", trim($input));

        $this->points = array_map(fn($point) => array_map('intval', explode(',', $point)), explode("\n", $points));
        $this->folds = array_map(function ($fold) {
            preg_match('/fold along (x|y)=(\d+)/', $fold, $matches);

            return [
                'axis' => $matches[1],
                'line' => (int) $matches[2],
            ];
        }, explode("\n", $folds));
    }

    public function solve(): string
    {
        $instructions = new Instructions($this->points);
        $instructions->fold($this->folds[0]['axis'], $this->folds[0]['line']);

        return $instructions->countPoints();
    }
}

class PartTwo extends PartOne
{
    public function solve(): string
    {
        $instructions = new Instructions($this->points);
        foreach($this->folds as $fold) {
            $instructions->fold($fold['axis'], $fold['line']);
        }

        return $instructions->print();
    }
}

class Instructions
{
    const X_AXIS = 'x';
    const Y_AXIS = 'y';

    protected array $grid;
    protected int $width = 0;
    protected int $height = 0;

    public function __construct(array $points)
    {
        foreach ($points as $point) {
            $this->grid[$point[1]][$point[0]] = true;

            $this->width = max([$this->width, $point[0] + 1]);
            $this->height = max([$this->height, $point[1] + 1]);
        }
    }

    public function fold(string $axis, int $line)
    {
        switch ($axis) {
            case static::X_AXIS: $this->foldX($line); break;
            case static::Y_AXIS: $this->foldY($line); break;
        }
    }

    public function countPoints(): int
    {
        return count($this->grid, COUNT_RECURSIVE) - count($this->grid);
    }

    public function print(): string
    {
        $output = '';

        ksort($this->grid);
        foreach ($this->grid as &$line) {
            ksort($line);
        }

        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                $output .= isset($this->grid[$y][$x]) ? '#' : '.';
            }
            $output .= "\n";
        }

        return $output;
    }

    protected function foldX(int $foldLine)
    {
        foreach ($this->grid as $y => $points) {
            for ($x = $foldLine + 1; $x < $this->width; $x++) {
                $newX = $foldLine - ($x - $foldLine);

                if (isset($this->grid[$y][$x])) {
                    $this->grid[$y][$newX] = true;
                    unset($this->grid[$y][$x]);
                }
            }
        }

        $this->width = $foldLine;
    }

    protected function foldY(int $foldLine)
    {
        for ($y = $foldLine + 1; $y < $this->height; $y++) {
            $points = array_keys($this->grid[$y] ?? []);
            unset($this->grid[$y]);
            $newY = $foldLine - ($y - $foldLine);

            foreach ($points as $x) {
                $this->grid[$newY][$x] = true;
            }
        }

        $this->height = $foldLine;
    }
}
