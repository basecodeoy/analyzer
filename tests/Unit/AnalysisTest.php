<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tests\Unit;

use BaseCodeOy\Analyzer\AnalysisTrait;
use PHPUnit\Framework\TestCase;

/**
 * This is the analysis test class.
 *
 * @internal
 */
final class AnalysisTest extends TestCase
{
    use AnalysisTrait;

    /**
     * Get the code paths to analyze.
     *
     * @return array<string>
     */
    #[\Override()]
    protected static function getPaths(): array
    {
        return [
            \realpath(__DIR__),
        ];
    }

    /**
     * Get the classes to ignore not existing.
     *
     * @return array<string>
     */
    private function getIgnored(): array
    {
        return [
            'Foo\\Bar',
            'Foo\\Baz',
        ];
    }
}
