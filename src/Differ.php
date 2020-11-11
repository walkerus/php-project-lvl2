<?php

declare(strict_types=1);

namespace Differ\Differ;

use Exception;

use function Funct\Collection\sortBy;
use function Differ\Parser\parse;
use function Differ\Formatters\format;
use function Funct\Collection\union;

const FORMAT_STYLISH = 'stylish';
const FORMAT_PLAIN = 'plain';
const DIFF_TYPE_DELETED = 'deleted';
const DIFF_TYPE_ADDED = 'added';
const DIFF_TYPE_CHANGED = 'changed';
const DIFF_TYPE_UNCHANGED = 'unchanged';
const DIFF_TYPE_NESTED = 'nested';

/**
 * @param string $firstFile
 * @param string $secondFile
 * @param string $format
 * @return string
 * @throws Exception
 */
function genDiff(string $firstFile, string $secondFile, string $format = FORMAT_STYLISH): string
{
    ['content' => $firstFileContent, 'extension' => $firstFileExt] = getFileData($firstFile);
    ['content' => $secondFileContent, 'extension' => $secondFileExt] = getFileData($secondFile);
    $diffTree = [
        'root',
        'children' => buildDiff(
            parse($firstFileContent, $firstFileExt),
            parse($secondFileContent, $secondFileExt)
        ),
    ];

    return format($diffTree, $format);
}

function buildDiff(array $data1, array $data2): array
{
    $keys = sortBy(union(array_keys($data1), array_keys($data2)), fn($v) => $v);

    return array_map(function (string $key) use ($data1, $data2): array {
        $value1 = $data1[$key] ?? null;
        $value2 = $data2[$key] ?? null;

        if (!array_key_exists($key, $data2)) {
            return [
                'key' => $key,
                'type' => DIFF_TYPE_DELETED,
                'value' => $value1,
            ];
        }

        if (!array_key_exists($key, $data1)) {
            return [
                'key' => $key,
                'type' => DIFF_TYPE_ADDED,
                'value' => $value2,
            ];
        }

        if (is_array($value1) && is_array($value2)) {
            return [
                'key' => $key,
                'type' => DIFF_TYPE_NESTED,
                'children' => buildDiff($value1, $value2),
            ];
        }

        if ($value1 != $value2) {
            return [
                'key' => $key,
                'type' => DIFF_TYPE_CHANGED,
                'value' => [$value1, $value2],
            ];
        }

        return [
            'key' => $key,
            'type' => DIFF_TYPE_UNCHANGED,
            'value' => $value1,
        ];
    }, $keys);
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
