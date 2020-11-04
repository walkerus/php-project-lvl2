<?php

declare(strict_types=1);

namespace Hexlet\Code\tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testDefault(): void
    {
        $this->assertStringEqualsFile(
            'tests/fixtures/diff',
            genDiff('tests/fixtures/file1.json', 'tests/fixtures/file2.json')
        );
    }
}
