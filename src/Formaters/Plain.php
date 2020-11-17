<?php

declare(strict_types=1);

namespace Differ\Formatters\Plain;

use Exception;

use const Differ\Differ\DIFF_TYPE_ADDED;
use const Differ\Differ\DIFF_TYPE_CHANGED;
use const Differ\Differ\DIFF_TYPE_DELETED;
use const Differ\Differ\DIFF_TYPE_NESTED;
use const Differ\Differ\DIFF_TYPE_UNCHANGED;

/**
 * @param array $diffTree
 * @param array $path
 * @return string
 * @throws Exception
 */
function format(array $diffTree, array $path = []): string
{
    $resultLines = array_map(function (array $diffTree) use ($path): ?string {
        $type = $diffTree['type'];
        $value = $diffTree['value'] ?? null;
        $currentPath = array_merge($path, [$diffTree['key']]);
        $stringCurrentPath = implode('.', $currentPath);

        switch ($type) {
            case DIFF_TYPE_UNCHANGED:
                return null;
            case DIFF_TYPE_DELETED:
                return "Property '$stringCurrentPath' was removed";
            case DIFF_TYPE_ADDED:
                return sprintf(
                    "Property '%s' was added with value: %s",
                    $stringCurrentPath,
                    formatValue($value)
                );
            case DIFF_TYPE_CHANGED:
                return sprintf(
                    "Property '%s' was updated. From %s to %s",
                    $stringCurrentPath,
                    formatValue($diffTree['values'][0]),
                    formatValue($diffTree['values'][1])
                );
            case DIFF_TYPE_NESTED:
                return format($diffTree, $currentPath);
            default:
                throw new Exception("Undefined type: $type");
        }
    }, $diffTree['children']);

    return implode("\n", array_filter($resultLines, fn(?string $v) => !is_null($v)));
}

function formatValue($value): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    if (is_array($value)) {
        return '[complex value]';
    }

    if (is_string($value)) {
        return "'$value'";
    }

    return (string)$value;
}
