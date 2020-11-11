<?php

declare(strict_types=1);

namespace Differ\Formatters\Json;

use Exception;

/**
 * @param array $diffTree
 * @return string
 * @throws Exception
 */
function format(array $diffTree): string
{
    return json_encode($diffTree, JSON_THROW_ON_ERROR);
}
