<?php

require __DIR__ . '/../Provisioning/vendor/autoload.php';

$file = count($argv) > 1 && in_array($argv[1], ['-s', '--sample']) ? 'sample.txt' : 'input.txt';

echo sprintf("Part one: %d\n", (new PartOne($file))->solve());
echo sprintf("Part two: %d\n", (new PartTwo($file))->solve());

class PartOne
{
    protected array $data;

    public function __construct(string $file)
    {
        $input = file_get_contents(__DIR__ . '/' . $file);
        $this->data = explode("\n", trim($input));
    }

    public function solve(): int
    {
        $number = new SnailFishNumber($this->data[0]);

        for ($n = 1; $n < count($this->data); $n++) {
            $number = $number->add(new SnailFishNumber($this->data[$n]));
        }

        return $number->magnitude();
    }
}

class PartTwo extends PartOne
{
    public function solve(): int
    {
        // TO DO
        return 0;
    }
}

class SnailFishNumber
{
}
