<?php

$file = count($argv) > 1 && in_array($argv[1], ['-s', '--sample']) ? 'sample.txt' : 'input.txt';

echo sprintf("Part one: %d\n", (new PartOne($file))->solve());
echo sprintf("Part two: %d\n", (new PartTwo($file))->solve());

class PartOne
{
    protected array $report;

    public function __construct(string $file)
    {
        $input = file_get_contents($file);
        $this->report = explode("\n", trim($input));
    }

    public function solve(): int
    {
        return (new Diagnostics($this->report))->getPowerConsumption();
    }
}

class PartTwo extends PartOne
{
    public function solve(): int
    {
        return (new Diagnostics($this->report))->getLifeSupportRating();
    }
}

class Diagnostics
{
    private array $report;

    public function __construct(array $report)
    {
        $this->report = $report;
    }

    public function getPowerConsumption(): int
    {
        return $this->getGammaRate() * $this->getEpsilonRate();
    }

    public function getGammaRate(): int
    {
        $rate = '';

        for ($n = 0; $n < strlen($this->report[0]); $n++) {
            $rate .= static::getMostCommonBit($n, $this->report);
        }

        return bindec($rate);
    }

    public function getEpsilonRate(): int
    {
        $rate = '';

        for ($n = 0; $n < strlen($this->report[0]); $n++) {
            $rate .= static::getLeastCommonBit($n, $this->report);
        }

        return bindec($rate);
    }

    public function getLifeSupportRating(): int
    {
        return $this->getOxigenGeneratorRating() * $this->getCO2ScrubberRating();
    }

    public function getOxigenGeneratorRating(): int
    {
        $filteredData = $this->report;

        for ($n = 0; $n < strlen($this->report[0]); $n++) {
            $filteredData = array_filter($filteredData, function($line) use ($n, $filteredData) {
                return substr($line, $n, 1) === static::getMostCommonBit($n, $filteredData);
            });

            if (count($filteredData) === 1) {
                return bindec(reset($filteredData));
            }
        }
    }

    public function getCO2ScrubberRating(): int
    {
        $filteredData = $this->report;

        for ($n = 0; $n < strlen($this->report[0]); $n++) {
            $filteredData = array_filter($filteredData, function($line) use ($n, $filteredData) {
                return substr($line, $n, 1) === static::getLeastCommonBit($n, $filteredData);
            });

            if (count($filteredData) === 1) {
                return bindec(reset($filteredData));
            }
        }
    }

    private static function getMostCommonBit(int $position, array $data): string
    {
        $bitCounts = static::getBitCounts($position, $data);

        return $bitCounts['zeroes'] <= $bitCounts['ones'] ? '1' : '0';
    }

    private static function getLeastCommonBit(int $position, array $data): string
    {
        $bitCounts = static::getBitCounts($position, $data);

        return $bitCounts['zeroes'] <= $bitCounts['ones'] ? '0' : '1';
    }

    private static function getBitCounts(int $position, array $data): array
    {
        $zeroes = 0;
        $ones = 0;

        foreach ($data as $line) {
            $bit = substr($line, $position, 1);
            if ($bit === '0') {
                $zeroes++;
            } else {
                $ones++;
            }
        }

        return compact('zeroes', 'ones');
    }
}
