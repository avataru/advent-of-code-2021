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
        $count = 0;
        foreach ($this->data as $line) {
            list($digits, $output) = explode(' | ', $line);
            $digits = explode(' ', $digits);
            $output = explode(' ', $output);

            $map = new Decoder($digits);
            $count += $map->count($output, [1, 4, 7, 8]);
        }

        return $count;
    }
}

class PartTwo extends PartOne
{
    public function solve(): int
    {
        $sum = 0;
        foreach ($this->data as $line) {
            list($digits, $output) = explode(' | ', $line);
            $digits = explode(' ', $digits);
            $output = explode(' ', $output);

            $map = new Decoder($digits);
            $sum += $map->decode($output);
        }

        return $sum;
    }
}

class Decoder
{
    protected array $map;

    public function __construct(array $digits)
    {
        $knownDigits = [];
        $digitsBySegments = [];
        foreach ($digits as $digit) {
            $length = strlen($digit);
            $digit = str_split($digit);
            sort($digit);
            $digitsBySegments[$length][implode('', $digit)] = $digit;

            switch ($length) {
                case 2: $knownDigits[1] = $digit; break;
                case 4: $knownDigits[4] = $digit; break;
                case 3: $knownDigits[7] = $digit; break;
                case 7: $knownDigits[8] = $digit; break;
            }
        }

        $sixSegmentNumbers = $digitsBySegments[6];
        foreach ($sixSegmentNumbers as $key => $number) {
            if (count(array_intersect($number, $knownDigits[4])) === 4) {
                $knownDigits[9] = $number;
                unset($sixSegmentNumbers[$key]);
            }
        }

        foreach ($sixSegmentNumbers as $key => $number) {
            if (count(array_intersect($number, $knownDigits[7])) === 3) {
                $knownDigits[0] = $number;
                unset($sixSegmentNumbers[$key]);
            }
        }

        $knownDigits[6] = reset($sixSegmentNumbers);

        $fiveSegmentNumbers = $digitsBySegments[5];
        foreach ($fiveSegmentNumbers as $key => $number) {
            if (count(array_intersect($number, $knownDigits[7])) === 3) {
                $knownDigits[3] = $number;
                unset($fiveSegmentNumbers[$key]);
            }
        }

        foreach ($fiveSegmentNumbers as $key => $number) {
            if (count(array_intersect($number, $knownDigits[6])) === 5) {
                $knownDigits[5] = $number;
                unset($fiveSegmentNumbers[$key]);
            }
        }

        $knownDigits[2] = reset($fiveSegmentNumbers);

        foreach ($knownDigits as $value => $digit) {
            sort($digit);
            $this->map[implode('', $digit)] = $value;
        }
    }

    public function count(array $numbers, array $digits): int
    {
        $count = 0;
        foreach ($numbers as $number) {
            $number = str_split($number);
            sort($number);
            $number = (int) $this->map[implode('', $number)];

            if (in_array($number, $digits)) {
                $count++;
            }
        }

        return $count;
    }

    public function decode(array $numbers): int
    {
        $output = '';
        foreach ($numbers as $number) {
            $number = str_split($number);
            sort($number);
            $number = implode('', $number);

            $output .= $this->map[$number];
        }

        return (int) $output;
    }
}
