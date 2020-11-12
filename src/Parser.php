<?php

declare(strict_types=1);

namespace Differ\Parser;

use Exception;
use Symfony\Component\Yaml\Yaml;

/**
 * @param string $data
 * @param string $format
 * @return array
 * @throws Exception
 */
function parse(string $data, string $format): array
{
    switch ($format) {
        case 'json':
            return json_decode($data, true);
        case 'yaml':
        case 'yml':
            return Yaml::parse($data);
        default:
            throw new Exception("Undefined format: $format");
    }
}
