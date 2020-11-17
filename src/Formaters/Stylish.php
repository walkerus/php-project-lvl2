<?php

declare(strict_types=1);

namespace Differ\Formatters\Stylish;

use Exception;

use const Differ\Differ\DIFF_TYPE_ADDED;
use const Differ\Differ\DIFF_TYPE_CHANGED;
use const Differ\Differ\DIFF_TYPE_DELETED;
use const Differ\Differ\DIFF_TYPE_NESTED;
use const Differ\Differ\DIFF_TYPE_UNCHANGED;

/**
 * @param array $diffTree
 * @param int $depth
 * @return string
 * @throws Exception
 */
function format(array $diffTree, int $depth = 1): string
{
    $resultLines = array_map(function (array $diffTree) use ($depth): string {
        $type = $diffTree['type'];
        $key = $diffTree['key'];
        $value = $diffTree['value'] ?? null;
        $indent = buildIndent($depth);
        $buildString = fn(string $prefix, string $value) => "$indent$prefix $key: $value";

        switch ($type) {
            case DIFF_TYPE_UNCHANGED:
                return $buildString(' ', formatValue($value, $depth));
            case DIFF_TYPE_DELETED:
                return $buildString('-', formatValue($value, $depth));
            case DIFF_TYPE_ADDED:
                return $buildString('+', formatValue($value, $depth));
            case DIFF_TYPE_CHANGED:
                return implode("\n", [
                    $buildString('-', formatValue($diffTree['values'][0], $depth)),
                    $buildString('+', formatValue($diffTree['values'][1], $depth))
                ]);
            case DIFF_TYPE_NESTED:
                $nestedString = format($diffTree, $depth + 1);
                return $buildString(' ', "{\n$nestedString\n$indent  }");
            default:
                throw new Exception("Undefined type: $type");
        }
    }, $diffTree['children']);

    $result = implode("\n", $resultLines);

    return $depth == 1
        ? "{\n$result\n}"
        : $result;
}

function buildIndent(int $depth): string
{
    return substr(str_repeat('    ', $depth), 2);
}

function formatValue($value, int $depth): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    if (is_array($value)) {
        $resultLines = array_map(function ($key, $v) use ($depth): string {
            $formattedValue = formatValue($v, $depth + 1);
            $indent = buildIndent($depth + 1);
            return "$indent  $key: $formattedValue";
        }, array_keys($value), $value);

        $result = implode("\n", $resultLines);
        $indent = buildIndent($depth);

        return "{\n$result\n$indent  }";
    }

    return (string)$value;
}
