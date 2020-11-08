<?php

declare(strict_types=1);

namespace Differ\Differ;

use Exception;

use function Funct\Collection\sortBy;
use function Differ\Parser\parse;

/**
 * @param string $firstFile
 * @param string $secondFile
 * @return string
 * @throws Exception
 */
function genDiff(string $firstFile, string $secondFile): string
{
    ['content' => $firstFileContent, 'extension' => $firstFileExt] = getFileData($firstFile);
    ['content' => $secondFileContent, 'extension' => $secondFileExt] = getFileData($secondFile);
    $diffs = buildDiff(parse($firstFileContent, $firstFileExt), parse($secondFileContent, $secondFileExt));
    $diffsAsString = implode("\n  ", $diffs);

    return "{\n  $diffsAsString\n}\n";
}

function buildDiff(array $data1, array $data2): array
{
    $keys = sortBy(array_keys($data1) + array_keys($data2), fn($v) => $v);

    return array_reduce(
        $keys,
        function (array $current, $key) use ($data1, $data2) {
            if (array_key_exists($key, $data1) && !array_key_exists($key, $data2)) {
                $current[] = diffFormat('-', $key, $data1[$key]);
            } elseif (!array_key_exists($key, $data1) && array_key_exists($key, $data2)) {
                $current[] = diffFormat('+', $key, $data2[$key]);
            } elseif ($data1[$key] != $data2[$key]) {
                $current[] = diffFormat('-', $key, $data1[$key]);
                $current[] = diffFormat('+', $key, $data2[$key]);
            } else {
                $current[] = diffFormat(' ', $key, $data1[$key]);
            }

            return $current;
        },
        [],
    );
}

function diffFormat(string $type, string $key, $value): string
{
    if (is_bool($value)) {
        $value = $value ? 'true' : 'false';
    }

    return sprintf('%s %s: %s', $type, $key, $value);
}

/**
 * @param string $filepath
 * @return array
 * @throws Exception
 */
function getFileData(string $filepath): array
{
    if (!file_exists($filepath)) {
        throw new Exception("File '$filepath' does not exist");
    }

    return [
        'content' => file_get_contents($filepath),
        'extension' => pathinfo($filepath, PATHINFO_EXTENSION)
    ];
}
