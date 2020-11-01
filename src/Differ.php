<?php

declare(strict_types=1);

namespace Differ\Differ;

use Exception;

use function Funct\Collection\sortBy;

/**
 * @param string $firstFile
 * @param string $secondFile
 * @return string
 * @throws Exception
 */
function genDiff(string $firstFile, string $secondFile): string
{
    $fileContent1 = getFileContent($firstFile);
    $fileContent2 = getFileContent($secondFile);
    $diffs = buildDiff(json_decode($fileContent1, true), json_decode($fileContent2, true));
    $diffsAsString = implode("\n  ", $diffs);

    return "{\n  $diffsAsString\n}\n";
}

function buildDiff(array $data1, array $data2): array
{
    $format = '%s %s: %s';
    $keys = sortBy(array_keys($data1) + array_keys($data2), fn($v) => $v);

    return array_reduce(
        $keys,
        function (array $current, $key) use ($data1, $data2, $format) {
            if (array_key_exists($key, $data1) && !array_key_exists($key, $data2)) {
                $current[] = sprintf($format, '-', $key, $data1[$key]);
            } elseif (!array_key_exists($key, $data1) && array_key_exists($key, $data2)) {
                $current[] = sprintf($format, '+', $key, $data2[$key]);
            } elseif ($data1[$key] != $data2[$key]) {
                $current[] = sprintf($format, '-', $key, $data1[$key]);
                $current[] = sprintf($format, '+', $key, $data2[$key]);
            } else {
                $current[] = sprintf($format, ' ', $key, $data1[$key]);
            }

            return $current;
        },
        [],
    );
}

/**
 * @param string $filepath
 * @return string
 * @throws Exception
 */
function getFileContent(string $filepath): string
{
    if (!file_exists($filepath)) {
        throw new Exception("File '$filepath' does not exist");
    }

    return file_get_contents($filepath);
}
