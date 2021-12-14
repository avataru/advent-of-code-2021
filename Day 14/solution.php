<?php

require __DIR__ . '/../Provisioning/vendor/autoload.php';

$file = count($argv) > 1 && in_array($argv[1], ['-s', '--sample']) ? 'sample.txt' : 'input.txt';

echo sprintf("Part one: %d\n", (new PartOne($file))->solve(10));
echo sprintf("Part two: %d\n", (new PartTwo($file))->solve(40));

class PartOne
{
    protected Polymer $polymer;

    public function __construct(string $file)
    {
        $input = file_get_contents(__DIR__ . '/' . $file);
        list($template, $lines) = explode("\n\n", trim($input));

        $instructions = [];
        foreach (explode("\n", $lines) as $instruction) {
            list($pair, $element) = explode(' -> ', $instruction);
            $instructions[$pair] = [substr($pair, 0, 1) . $element, $element . substr($pair, 1, 1)];
        }

        $this->polymer = new Polymer($template, $instructions);
    }

    public function solve(int $steps): int
    {
        for ($n = 1; $n <= $steps; $n++) {
            $this->polymer->runStep();
        }

        return $this->polymer->value();
    }
}

class PartTwo extends PartOne
{
}

class Polymer
{
    private string $template;
    private array $pairs;
    private array $instructions;

    public function __construct(string $template, array $instructions)
    {
        $this->template = $template;

        for ($n = 0; $n < strlen($template) - 1; $n++) {
            $pair = substr($template, $n, 2);

            if (!isset($this->pairs[$pair])) {
                $this->pairs[$pair] = 1;
            } else {
                $this->pairs[$pair]++;
            }
        }

        $this->instructions = $instructions;
    }

    public function runStep()
    {
        $newPairs = [];
        foreach ($this->pairs as $pair => $count) {
            foreach ($this->instructions[$pair] ?? [] as $instruction) {
                if (!isset($newPairs[$instruction])) {
                    $newPairs[$instruction] = $count;
                } else {
                    $newPairs[$instruction] += $count;
                }
            }
        }
        $this->pairs = $newPairs;
    }

    public function value(): int
    {
        $elements = [substr($this->template, -1, 1) => 1];
        foreach ($this->pairs as $pair => $count) {
            $element = substr($pair, 0, 1);

            if (!isset($elements[$element])) {
                $elements[$element] = $count;
            } else {
                $elements[$element] += $count;
            }
        }

        rsort($elements);

        return reset($elements) - end($elements);
    }
}
