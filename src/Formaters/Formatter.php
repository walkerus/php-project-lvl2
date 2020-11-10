<?php

declare(strict_types=1);

namespace Differ\Formatters;

use Exception;

use function Differ\Formatters\Stylish\format as stylishFormat;

use const Differ\Differ\FORMAT_STYLISH;

/**
 * @param array $diffTree
 * @param string $format
 * @return string
 * @throws Exception
 */
function format(array $diffTree, string $format): string
{
    switch ($format) {
        case FORMAT_STYLISH:
            return stylishFormat($diffTree);
        default:
            throw new Exception("Undefined format: $format");
    }
}
