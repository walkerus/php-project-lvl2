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
     * @param string $expected
     * @param string $format
     * @param array $filesCombination
     * @throws Exception
     */
    public function testGenDiff(string $expected, string $format, array $filesCombination): void
    {
        foreach ($filesCombination as $files) {
            $this->assertStringEqualsFile($expected, genDiff($files[0], $files[1], $format));
        }
    }

    public function dataProvider(): array
    {
        $filesCombination = [
            [
                $this->getFixtureFullPath('file1.json'),
                $this->getFixtureFullPath('file2.json'),
            ],
            [
                $this->getFixtureFullPath('file1.json'),
                $this->getFixtureFullPath('file2.yml'),
            ],
            [
                $this->getFixtureFullPath('file1.yml'),
                $this->getFixtureFullPath('file2.yml'),
            ],
        ];

        return [
            'stylish' => [
                'expected' => $this->getFixtureFullPath('diff.stylish'),
                'format' => 'stylish',
                'filesCombination' => $filesCombination,
            ],
            'plain' => [
                'expected' => $this->getFixtureFullPath('diff.plain'),
                'format' => 'plain',
                'filesCombination' => $filesCombination
            ],
            'json' => [
                'expected' => $this->getFixtureFullPath('diff.json'),
                'format' => 'json',
                'filesCombination' => $filesCombination
            ],
        ];
    }

    private function getFixtureFullPath($fixtureName): string
    {
        return realpath(implode(DIRECTORY_SEPARATOR, [__DIR__, 'fixtures', $fixtureName]));
    }
}
