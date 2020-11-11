<?php

declare(strict_types=1);

namespace Differ\Formatters;

use Exception;

use function Differ\Formatters\Stylish\format as stylishFormat;
use function Differ\Formatters\Plain\format as plainFormat;
use function Differ\Formatters\Json\format as jsonFormat;

use const Differ\Differ\FORMAT_PLAIN;
use const Differ\Differ\FORMAT_STYLISH;
use const Differ\Differ\FORMAT_JSON;

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
        case FORMAT_PLAIN:
            return plainFormat($diffTree);
        case FORMAT_JSON:
            return jsonFormat($diffTree);
        default:
            throw new Exception("Undefined format: $format");
    }
}
