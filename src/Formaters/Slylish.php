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
    $resultStrings = [];
    foreach ($diffTree['children'] as $child) {
        $type = $child['type'];
        $key = $child['key'];
        $value = $child['value'];
        $indent = buildIndent($depth);
        $buildString = fn(string $prefix, string $value) => "$indent$prefix $key: $value";

        switch ($type) {
            case DIFF_TYPE_UNCHANGED:
                $resultStrings[] = $buildString(' ', formatValue($value, $depth));
                break;
            case DIFF_TYPE_DELETED:
                $resultStrings[] = $buildString('-', formatValue($value, $depth));
                break;
            case DIFF_TYPE_ADDED:
                $resultStrings[] = $buildString('+', formatValue($value, $depth));
                break;
            case DIFF_TYPE_CHANGED:
                $resultStrings[] = $buildString('-', formatValue($value[0], $depth));
                $resultStrings[] = $buildString('+', formatValue($value[1], $depth));
                break;
            case DIFF_TYPE_NESTED:
                $nestedString = format($child, $depth + 1);
                $resultStrings[] = $buildString(' ', "{\n$nestedString\n$indent  }");
                break;
            default:
                throw new Exception("Undefined type: $type");
        }
    }

    $resultString = implode("\n", $resultStrings);

    return $depth == 1
        ? "{\n$resultString\n}"
        : $resultString;
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
        $resultStrings = [];
        foreach ($value as $key => $v) {
            $formattedValue = formatValue($v, $depth + 1);
            $indent = buildIndent($depth + 1);
            $resultStrings[] = "$indent  $key: $formattedValue";
        }
        $resultString = implode("\n", $resultStrings);
        $indent = buildIndent($depth);

        return "{\n$resultString\n$indent  }";
    }

    return (string) $value;
}
