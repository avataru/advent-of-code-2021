<?php

require __DIR__ . '/../Provisioning/vendor/autoload.php';

$file = count($argv) > 1 && in_array($argv[1], ['-s', '--sample']) ? 'sample.txt' : 'input.txt';
$index = $argv[2] ?? null;

echo sprintf("Part one: %d\n", (new PartOne($file))->solve($index));
echo sprintf("Part two: %d\n", (new PartTwo($file))->solve($index));

class PartOne
{
    const KEY = [
        '0' => '0000', '1' => '0001', '2' => '0010', '3' => '0011', '4' => '0100',
        '5' => '0101', '6' => '0110', '7' => '0111', '8' => '1000', '9' => '1001',
        'A' => '1010', 'B' => '1011', 'C' => '1100', 'D' => '1101', 'E' => '1110',
        'F' => '1111',
    ];

    protected array $data;

    public function __construct(string $file)
    {
        $input = file_get_contents(__DIR__ . '/' . $file);
        $this->data = explode("\n", trim($input));
    }

    public function solve(?int $message = 0): int
    {
        $data = $this->data[$message] ?? $this->data[0];
        $data = str_replace(array_keys(static::KEY), array_values(static::KEY), $data);
        $packet = new Packet($data);

        return $packet->versionSum;
    }
}

class PartTwo extends PartOne
{
    public function solve(?int $message = 0): int
    {
        $data = $this->data[$message] ?? $this->data[0];
        $data = str_replace(array_keys(static::KEY), array_values(static::KEY), $data);
        $packet = new Packet($data);

        return $packet->value;
    }
}

class Packet
{
    const TYPE_LITERAL = 4;

    public int $version;
    public int $typeId;
    public int $length = 0;
    private string $data;
    private $contents;

    public int $versionSum = 0;
    public int $value = 0;

    public function __construct(string $data)
    {
        $this->data = $data;
        $this->length = strlen($data);

        $this->version = bindec(substr($data, 0, 3));
        $this->typeId = bindec(substr($data, 3, 3));

        $this->contents = $this->unpack($data);

        $this->versionSum += $this->version;
    }

    private function unpack(string $data)
    {
        if ($this->isLiteral()) {
            return $this->getLiteralValue($data);
        }

        return $this->getSubpackets($data);
    }

    private function isLiteral(): bool
    {
        return $this->typeId === static::TYPE_LITERAL;
    }

    private function isOperator(): bool
    {
        return $this->typeId !== static::TYPE_LITERAL;
    }

    private function getLiteralValue(string $data): int
    {
        $value = '';
        $start = 6;
        while (substr($data, $start, 1) === '1') {
            $value .= substr($data, $start + 1, 4);
            $start += 5;
        }
        $value .= substr($data, $start + 1, 4);

        $this->length = $start + 5;
        $this->data = substr($this->data, 0, $this->length);
        $this->value = bindec($value);

        return $this->value;
    }

    private function getSubpackets(string $data): array
    {
        $this->length = 7;

        $values = [];

        $subpackets = [];
        $lengthTypeId = substr($data, 6, 1);

        if ($lengthTypeId === '0') {
            $length = bindec(substr($data, 7, 15));

            $start = 22;
            while ($start < $length + $start) {
                $payload = substr($data, $start, $length);

                $packet = new Packet($payload);
                $subpackets[] = $packet;

                $start += $packet->length;
                $length -= $packet->length;

                $this->length += $packet->length;
                $this->versionSum += $packet->versionSum;

                $values[] = $packet->value;
            }

            $this->length += 15;
        } else {
            $count = bindec(substr($data, 7, 11));

            $start = 18;
            for ($n = 1; $n <= $count; $n++) {
                $payload = substr($data, $start);

                $packet = new Packet($payload);
                $subpackets[] = $packet;

                $start += $packet->length;

                $this->length += $packet->length;
                $this->versionSum += $packet->versionSum;

                $values[] = $packet->value;
            }

            $this->length += 11;
        }

        switch ($this->typeId) {
            case 0: $this->value = array_sum($values); break;
            case 1: $this->value = array_product($values); break;
            case 2: $this->value = min($values); break;
            case 3: $this->value = max($values); break;
            case 5: $this->value = $values[0] > $values[1] ? 1 : 0; break;
            case 6: $this->value = $values[0] < $values[1] ? 1 : 0; break;
            case 7: $this->value = $values[0] == $values[1] ? 1 : 0; break;
        }

        return $subpackets;
    }
}
