<?php

namespace App\Services;

use InvalidArgumentException;

class QrCodeService
{
    private const VERSION = 2;

    private const SIZE = 25;

    private const DATA_CODEWORDS = 34;

    private const EC_CODEWORDS = 10;

    private const ALPHANUMERIC = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ $%*+-./:';

    /**
     * @return array<int, array<int, bool>>
     */
    public function matrix(string $value): array
    {
        $value = strtoupper(trim($value));

        if ($value === '') {
            throw new InvalidArgumentException('QR value cannot be empty.');
        }

        $codewords = $this->codewords($value);
        $bits = $this->bytesToBits([...$codewords, ...$this->reedSolomon($codewords)]);
        $matrix = array_fill(0, self::SIZE, array_fill(0, self::SIZE, null));
        $reserved = array_fill(0, self::SIZE, array_fill(0, self::SIZE, false));

        $this->addFunctionPatterns($matrix, $reserved);
        $this->addData($matrix, $reserved, $bits);
        $this->addFormatInfo($matrix, $reserved);

        return array_map(
            fn (array $row): array => array_map(fn ($module): bool => (bool) $module, $row),
            $matrix,
        );
    }

    public function svg(string $value, int $scale = 6): string
    {
        $matrix = $this->matrix($value);
        $quiet = 4;
        $size = (self::SIZE + ($quiet * 2)) * $scale;
        $parts = [
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '.$size.' '.$size.'" width="'.$size.'" height="'.$size.'" role="img" aria-label="Patient QR code">',
            '<rect width="100%" height="100%" fill="#fff"/>',
        ];

        foreach ($matrix as $rowIndex => $row) {
            foreach ($row as $columnIndex => $dark) {
                if (! $dark) {
                    continue;
                }

                $x = ($columnIndex + $quiet) * $scale;
                $y = ($rowIndex + $quiet) * $scale;
                $parts[] = '<rect x="'.$x.'" y="'.$y.'" width="'.$scale.'" height="'.$scale.'" fill="#111827"/>';
            }
        }

        $parts[] = '</svg>';

        return implode('', $parts);
    }

    /**
     * @return array<int, int>
     */
    private function codewords(string $value): array
    {
        $charMap = array_flip(str_split(self::ALPHANUMERIC));
        $bits = '0010'.str_pad(decbin(strlen($value)), 9, '0', STR_PAD_LEFT);

        for ($index = 0; $index < strlen($value); $index += 2) {
            $first = $charMap[$value[$index]] ?? null;

            if ($first === null) {
                throw new InvalidArgumentException('QR value contains unsupported characters.');
            }

            if (isset($value[$index + 1])) {
                $second = $charMap[$value[$index + 1]] ?? null;

                if ($second === null) {
                    throw new InvalidArgumentException('QR value contains unsupported characters.');
                }

                $bits .= str_pad(decbin(($first * 45) + $second), 11, '0', STR_PAD_LEFT);
            } else {
                $bits .= str_pad(decbin($first), 6, '0', STR_PAD_LEFT);
            }
        }

        $bits .= str_repeat('0', min(4, (self::DATA_CODEWORDS * 8) - strlen($bits)));
        $bits .= str_repeat('0', (8 - (strlen($bits) % 8)) % 8);

        $codewords = [];

        foreach (str_split($bits, 8) as $byte) {
            $codewords[] = bindec($byte);
        }

        $padBytes = [0xEC, 0x11];
        $padIndex = 0;

        while (count($codewords) < self::DATA_CODEWORDS) {
            $codewords[] = $padBytes[$padIndex % 2];
            $padIndex++;
        }

        return $codewords;
    }

    /**
     * @param  array<int, int>  $bytes
     * @return array<int, int>
     */
    private function reedSolomon(array $bytes): array
    {
        $generator = [1, 251, 67, 46, 61, 118, 70, 64, 94, 32, 45];
        $message = array_merge($bytes, array_fill(0, self::EC_CODEWORDS, 0));

        for ($index = 0; $index < count($bytes); $index++) {
            $coefficient = $message[$index];

            if ($coefficient === 0) {
                continue;
            }

            foreach ($generator as $generatorIndex => $generatorCoefficient) {
                $message[$index + $generatorIndex] ^= $this->gfMultiply($coefficient, $generatorCoefficient);
            }
        }

        return array_slice($message, -self::EC_CODEWORDS);
    }

    private function gfMultiply(int $left, int $right): int
    {
        $result = 0;

        while ($right > 0) {
            if (($right & 1) !== 0) {
                $result ^= $left;
            }

            $left <<= 1;

            if (($left & 0x100) !== 0) {
                $left ^= 0x11D;
            }

            $right >>= 1;
        }

        return $result & 0xFF;
    }

    /**
     * @param  array<int, int>  $bytes
     * @return array<int, int>
     */
    private function bytesToBits(array $bytes): array
    {
        return collect($bytes)
            ->flatMap(fn (int $byte): array => array_map('intval', str_split(str_pad(decbin($byte), 8, '0', STR_PAD_LEFT))))
            ->all();
    }

    /**
     * @param  array<int, array<int, bool|null>>  $matrix
     * @param  array<int, array<int, bool>>  $reserved
     */
    private function addFunctionPatterns(array &$matrix, array &$reserved): void
    {
        $this->addFinder($matrix, $reserved, 0, 0);
        $this->addFinder($matrix, $reserved, self::SIZE - 7, 0);
        $this->addFinder($matrix, $reserved, 0, self::SIZE - 7);
        $this->addAlignment($matrix, $reserved, 18, 18);

        for ($index = 8; $index < self::SIZE - 8; $index++) {
            $this->setFunction($matrix, $reserved, 6, $index, $index % 2 === 0);
            $this->setFunction($matrix, $reserved, $index, 6, $index % 2 === 0);
        }

        $this->setFunction($matrix, $reserved, 8, 17, true);

        for ($index = 0; $index < 9; $index++) {
            if ($index !== 6) {
                $reserved[8][$index] = true;
                $reserved[$index][8] = true;
            }
        }

        for ($index = self::SIZE - 8; $index < self::SIZE; $index++) {
            $reserved[8][$index] = true;
            $reserved[$index][8] = true;
        }
    }

    /**
     * @param  array<int, array<int, bool|null>>  $matrix
     * @param  array<int, array<int, bool>>  $reserved
     */
    private function addFinder(array &$matrix, array &$reserved, int $x, int $y): void
    {
        for ($row = -1; $row <= 7; $row++) {
            for ($column = -1; $column <= 7; $column++) {
                $matrixY = $y + $row;
                $matrixX = $x + $column;

                if ($matrixY < 0 || $matrixY >= self::SIZE || $matrixX < 0 || $matrixX >= self::SIZE) {
                    continue;
                }

                $dark = ($row >= 0 && $row <= 6 && $column >= 0 && $column <= 6)
                    && ($row === 0 || $row === 6 || $column === 0 || $column === 6 || ($row >= 2 && $row <= 4 && $column >= 2 && $column <= 4));

                $this->setFunction($matrix, $reserved, $matrixX, $matrixY, $dark);
            }
        }
    }

    /**
     * @param  array<int, array<int, bool|null>>  $matrix
     * @param  array<int, array<int, bool>>  $reserved
     */
    private function addAlignment(array &$matrix, array &$reserved, int $centerX, int $centerY): void
    {
        for ($row = -2; $row <= 2; $row++) {
            for ($column = -2; $column <= 2; $column++) {
                $dark = max(abs($row), abs($column)) !== 1;
                $this->setFunction($matrix, $reserved, $centerX + $column, $centerY + $row, $dark);
            }
        }
    }

    /**
     * @param  array<int, array<int, bool|null>>  $matrix
     * @param  array<int, array<int, bool>>  $reserved
     */
    private function setFunction(array &$matrix, array &$reserved, int $x, int $y, bool $dark): void
    {
        $matrix[$y][$x] = $dark;
        $reserved[$y][$x] = true;
    }

    /**
     * @param  array<int, array<int, bool|null>>  $matrix
     * @param  array<int, array<int, bool>>  $reserved
     * @param  array<int, int>  $bits
     */
    private function addData(array &$matrix, array $reserved, array $bits): void
    {
        $bitIndex = 0;
        $upward = true;

        for ($right = self::SIZE - 1; $right >= 1; $right -= 2) {
            if ($right === 6) {
                $right--;
            }

            for ($vertical = 0; $vertical < self::SIZE; $vertical++) {
                $row = $upward ? self::SIZE - 1 - $vertical : $vertical;

                for ($columnOffset = 0; $columnOffset < 2; $columnOffset++) {
                    $column = $right - $columnOffset;

                    if ($reserved[$row][$column]) {
                        continue;
                    }

                    $bit = $bits[$bitIndex] ?? 0;
                    $dark = $bit === 1;

                    if (($row + $column) % 2 === 0) {
                        $dark = ! $dark;
                    }

                    $matrix[$row][$column] = $dark;
                    $bitIndex++;
                }
            }

            $upward = ! $upward;
        }
    }

    /**
     * @param  array<int, array<int, bool|null>>  $matrix
     * @param  array<int, array<int, bool>>  $reserved
     */
    private function addFormatInfo(array &$matrix, array &$reserved): void
    {
        $bits = array_map('intval', str_split('101010000010010'));
        $positionsA = [[8, 0], [8, 1], [8, 2], [8, 3], [8, 4], [8, 5], [8, 7], [8, 8], [7, 8], [5, 8], [4, 8], [3, 8], [2, 8], [1, 8], [0, 8]];
        $positionsB = [[self::SIZE - 1, 8], [self::SIZE - 2, 8], [self::SIZE - 3, 8], [self::SIZE - 4, 8], [self::SIZE - 5, 8], [self::SIZE - 6, 8], [self::SIZE - 7, 8], [8, self::SIZE - 8], [8, self::SIZE - 7], [8, self::SIZE - 6], [8, self::SIZE - 5], [8, self::SIZE - 4], [8, self::SIZE - 3], [8, self::SIZE - 2], [8, self::SIZE - 1]];

        foreach ($bits as $index => $bit) {
            [$xA, $yA] = $positionsA[$index];
            [$xB, $yB] = $positionsB[$index];
            $this->setFunction($matrix, $reserved, $xA, $yA, $bit === 1);
            $this->setFunction($matrix, $reserved, $xB, $yB, $bit === 1);
        }
    }
}
