<?php

require __DIR__ . '/../Provisioning/vendor/autoload.php';

$file = 'input.txt';
if (count($argv) > 1) {
    switch ($argv[1]) {
        case '-s1':
        case '--sample=1':
            $file = 'sample1.txt';
            break;
        case '-s2':
        case '--sample=2':
            $file = 'sample2.txt';
            break;
        case '-s3':
        case '--sample=3':
            $file = 'sample3.txt';
            break;
    }
}

echo sprintf("Part one: %d\n", (new PartOne($file))->solve());
echo sprintf("Part two: %d\n", (new PartTwo($file))->solve());

class PartOne
{
    protected array $caves = [];

    public function __construct(string $file)
    {
        $input = file_get_contents(__DIR__ . '/' . $file);
        foreach (explode("\n", trim($input)) as $line) {
            list($firstCaveName, $secondCaveName) = explode('-', $line);

            if ($secondCaveName !== 'start') {
                $this->caves[$firstCaveName][$secondCaveName] = $secondCaveName;
            }

            if ($firstCaveName !== 'start') {
                $this->caves[$secondCaveName][$firstCaveName] = $firstCaveName;
            }
        }
    }

    public function solve(): int
    {
        $paths = $this->getPaths('start', ['start'], false);
        // dump(array_map(fn($path) => implode(',', $path), $paths));

        return count($paths);
    }

    protected function getPaths(string $previousCave, array $visitedCaves, bool $allowRevisits = false): array
    {
        if ($previousCave === 'end') {
            return [$visitedCaves];
        }

        $paths = [];

        foreach ($this->caves[$previousCave] as $cave) {
            if (!(ctype_lower($cave) && in_array($cave, $visitedCaves))) {
                $temp = array_merge($visitedCaves, [$cave]);
                $paths = array_merge($paths, $this->getPaths($cave, $temp, $allowRevisits));
            } elseif ($allowRevisits && ctype_lower($cave) && in_array($cave, $visitedCaves)) {
                $temp = array_merge($visitedCaves, [$cave]);
                $paths = array_merge($paths, $this->getPaths($cave, $temp, false));
            }
        }

        return $paths;
    }
}

class PartTwo extends PartOne
{
    public function solve(): int
    {
        $paths = $this->getPaths('start', ['start'], true);
        // dump(array_map(fn($path) => implode(',', $path), $paths));

        return count($paths);
    }
}
