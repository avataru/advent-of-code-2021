<?php

$file = count($argv) > 1 && in_array($argv[1], ['-s', '--sample']) ? 'sample.txt' : 'input.txt';

echo sprintf("Part one: %d\n", (new PartOne($file))->solve());
echo sprintf("Part two: %d\n", (new PartTwo($file))->solve());

class PartOne
{
    const ROUND_BRACKETS_SCORE = 3;
    const SQUARE_BRACKETS_SCORE = 57;
    const CURLY_BRACKETS_SCORE = 1197;
    const ANGLE_BRACKETS_SCORE = 25137;

    protected array $data;

    public function __construct(string $file)
    {
        $input = file_get_contents($file);
        $this->data = explode("\n", trim($input));
    }

    public function solve(): int
    {
        $score = 0;
        foreach ($this->data as $line) {
            $line = static::removeValidChunks($line);
            $score += static::getCorruptionScore($line);
        }

        return $score;
    }

    protected static function removeValidChunks(string $line): string
    {
        while (strpos($line, '()') !== false
            || strpos($line, '[]') !== false
            || strpos($line, '{}') !== false
            || strpos($line, '<>') !== false
        ){
            $line = preg_replace('/\(\)|\[\]|\{\}|<>/', '', $line);
        }

        return $line;
    }

    private static function getCorruptionScore(string $line): int
    {
        $corruptedCharacter = static::getCorruption($line);
        if (!is_null($corruptedCharacter)) {
            switch ($corruptedCharacter) {
                case ')': return static::ROUND_BRACKETS_SCORE;
                case ']': return static::SQUARE_BRACKETS_SCORE;
                case '}': return static::CURLY_BRACKETS_SCORE;
                case '>': return static::ANGLE_BRACKETS_SCORE;
            }
        }

        return 0;
    }

    protected static function getCorruption(string $line): ?string
    {
        preg_match('/(?<=[\(\[\{<])([\)\]}>])/', $line, $matches);

        return $matches[1] ?? null;
    }
}

class PartTwo extends PartOne
{
    const ROUND_BRACKETS_SCORE = 1;
    const SQUARE_BRACKETS_SCORE = 2;
    const CURLY_BRACKETS_SCORE = 3;
    const ANGLE_BRACKETS_SCORE = 4;

    public function solve(): int
    {
        $scores = [];
        foreach ($this->data as $line) {
            $line = static::removeValidChunks($line);
            if (static::isIncomplete($line)) {
                array_push($scores, static::getCompletionScore($line));
            }
        }
        sort($scores);

        return $scores[floor(count($scores) / 2)];
    }

    private static function isIncomplete(string $line): bool
    {
        return is_null(static::getCorruption($line));
    }

    private static function getCompletionScore(string $line): int
    {
        $score = 0;
        foreach (array_reverse(str_split($line)) as $character) {
            $score *= 5;
            switch ($character) {
                case '(': $score += static::ROUND_BRACKETS_SCORE; break;
                case '[': $score += static::SQUARE_BRACKETS_SCORE; break;
                case '{': $score += static::CURLY_BRACKETS_SCORE; break;
                case '<': $score += static::ANGLE_BRACKETS_SCORE; break;
            }
        }

        return $score;
    }
}
