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
    $resultStrings = [];
    foreach ($diffTree['children'] as $child) {
        $type = $child['type'];
        $value = $child['value'] ?? null;
        $currentPath = array_merge($path, [$child['key']]);
        $stringCurrentPath = implode('.', $currentPath);

        switch ($type) {
            case DIFF_TYPE_UNCHANGED:
                break;
            case DIFF_TYPE_DELETED:
                $resultStrings[] = "Property '$stringCurrentPath' was removed";
                break;
            case DIFF_TYPE_ADDED:
                $resultStrings[] = sprintf(
                    "Property '%s' was added with value: %s",
                    $stringCurrentPath,
                    formatValue($value)
                );
                break;
            case DIFF_TYPE_CHANGED:
                $resultStrings[] = sprintf(
                    "Property '%s' was updated. From %s to %s",
                    $stringCurrentPath,
                    formatValue($value[0]),
                    formatValue($value[1])
                );
                break;
            case DIFF_TYPE_NESTED:
                $resultStrings[] = format($child, $currentPath);
                break;
            default:
                throw new Exception("Undefined type: $type");
        }
    }

    return implode("\n", $resultStrings);
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

    return (string) $value;
}
