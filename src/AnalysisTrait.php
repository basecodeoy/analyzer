<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace BaseCodeOy\Analyzer;

/**
 * This is the analysis trait.
 */
trait AnalysisTrait
{
    /**
     * Get the files to check.
     *
     * @return array<array<string>>
     */
    public static function provideFilesToCheck(): array
    {
        $iterator = new \AppendIterator();

        foreach (static::getPaths() as $path) {
            $iterator->append(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)));
        }

        $files = new \CallbackFilterIterator($iterator, fn ($file): bool => $file->getFilename()[0] !== '.' && !$file->isDir() && static::shouldAnalyzeFile($file));

        return \array_map(fn ($file): array => [(string) $file], \iterator_to_array($files));
    }

    /**
     * Test all class references exist.
     *
     * @dataProvider provideFilesToCheck
     */
    public function testReferences(string $file): void
    {
        static::assertTrue(\file_exists($file), \sprintf('Expected %s to exist.', $file));

        $ignored = \method_exists($this, 'getIgnored') ? static::getIgnored() : [];

        foreach ((new ReferenceAnalyzer())->analyze($file) as $class) {
            if (\in_array($class, $ignored, true)) {
                continue;
            }

            static::assertTrue(ClassInspector::inspect($class)->exists(), \sprintf('Expected %s to exist.', $class));
        }
    }

    /**
     * Determine if the given file should be analyzed.
     */
    protected static function shouldAnalyzeFile(\SplFileInfo $file): bool
    {
        return \str_ends_with($file->getFilename(), '.php');
    }

    /**
     * Get the code paths to analyze.
     *
     * @return array<string>
     */
    abstract protected static function getPaths(): array;
}
