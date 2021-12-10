<?php

$file = count($argv) > 1 && in_array($argv[1], ['-s', '--sample']) ? 'sample.txt' : 'input.txt';

echo sprintf("Part one: %d\n", (new PartOne($file))->solve());
echo sprintf("Part two: %d\n", (new PartTwo($file))->solve());

class PartOne
{
    protected array $numbers;
    protected array $boards;

    public function __construct(string $file)
    {
        $input = file_get_contents($file);
        $boards = explode("\n\n", $input);

        $this->numbers = explode(',', array_shift($boards));
        $this->boards = array_map(fn($board) => new Board($board), $boards);
    }

    public function solve(): int
    {
        for ($n = 0; $n < count($this->numbers); $n++) {
            $drawnNumbers = array_slice($this->numbers, 0, $n + 1);

            foreach ($this->boards as $board) {
                if ($board->hasBingo($drawnNumbers)) {
                    return $board->score;
                }
            }
        }
    }
}

class PartTwo extends PartOne
{
    public function solve(): int
    {
        $score = 0;

        for ($n = 0; $n < count($this->numbers); $n++) {
            $drawnNumbers = array_slice($this->numbers, 0, $n + 1);

            foreach ($this->boards as $board) {
                if (!$board->hasWon() && $board->hasBingo($drawnNumbers)) {
                    $score = $board->score;
                }
            }
        }

        return $score;
    }
}

class Board
{
    private int $size;

    public array $lines = [];
    public $score = null;

    public function __construct(string $data, int $size = 5)
    {
        $this->size = $size;

        $numbers = explode(',', preg_replace('/\s+/', ',', trim($data)));
        $this->lines = array_chunk($numbers, $this->size);
    }

    public function hasBingo(array $numbers): bool
    {
        if ($this->hasWon()) {
            return true;
        }

        $possibleLines = $this->lines;

        for ($i = 0; $i < $this->size; $i++) {
            $column = [];
            foreach ($this->lines as $row) {
                array_push($column, $row[$i]);
            }
            array_push($possibleLines, $column);
        }

        foreach ($possibleLines as $line) {
            if (count(array_intersect($numbers, $line)) === $this->size) {
                $this->updateScore($numbers);

                return true;
            }
        }

        return false;
    }

    public function hasWon()
    {
        return !is_null($this->score);
    }

    private function updateScore(array $numbers)
    {
        $boardNumbers = $this->getNumbers();
        $sum = array_sum($boardNumbers) - array_sum(array_intersect($numbers, $boardNumbers));
        $this->score = $sum * end($numbers);
    }

    private function getNumbers()
    {
        return array_reduce($this->lines, function ($lines, $line) {
            return array_merge($lines, $line);
        }, []);
    }
}
