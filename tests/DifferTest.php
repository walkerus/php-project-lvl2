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
     * @param array $filePathsCombinations
     * @throws Exception
     */
    public function testGenDiff(string $expected, string $format, array $filePathsCombinations): void
    {
        foreach ($filePathsCombinations as $filePathsCombination) {
            $this->assertStringEqualsFile(
                $expected,
                genDiff($filePathsCombination[0], $filePathsCombination[1], $format)
            );
        }
    }

    public function dataProvider(): array
    {
        $filePathsCombinations = [
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
                'filesCombination' => $filePathsCombinations,
            ],
            'plain' => [
                'expected' => $this->getFixtureFullPath('diff.plain'),
                'format' => 'plain',
                'filesCombination' => $filePathsCombinations
            ],
            'json' => [
                'expected' => $this->getFixtureFullPath('diff.json'),
                'format' => 'json',
                'filesCombination' => $filePathsCombinations
            ],
        ];
    }

    private function getFixtureFullPath($fixtureName)
    {
        return realpath(implode(DIRECTORY_SEPARATOR, [__DIR__, 'fixtures', $fixtureName]));
    }
}
