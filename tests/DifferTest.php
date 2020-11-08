<?php

declare(strict_types=1);

namespace Hexlet\Code\tests;

use Exception;
use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     * @param string $expectedFile
     * @param string $firstFile
     * @param string $secondFile
     * @throws Exception
     */
    public function testGenDiff(string $expectedFile, string $firstFile, string $secondFile): void
    {
        $this->assertStringEqualsFile($expectedFile, genDiff($firstFile, $secondFile));
    }

    public function dataProvider(): array
    {
        return [
            'json' => [
                'expectedFile' => $this->getFixtureFullPath('diff'),
                'firstFile' => $this->getFixtureFullPath('file1.json'),
                'secondFile' => $this->getFixtureFullPath('file2.json')
            ],
            'json and yml' => [
                'expectedFile' => $this->getFixtureFullPath('diff'),
                'firstFile' => $this->getFixtureFullPath('file1.json'),
                'secondFile' => $this->getFixtureFullPath('file2.yml')
            ],
            'yml' => [
                'expectedFile' => $this->getFixtureFullPath('diff'),
                'firstFile' => $this->getFixtureFullPath('file1.yml'),
                'secondFile' => $this->getFixtureFullPath('file2.yml')
            ],
        ];
    }

    // Может быть методом, но не обязательно
    private function getFixtureFullPath($fixtureName)
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];

        return realpath(implode(DIRECTORY_SEPARATOR, $parts));
    }
}
