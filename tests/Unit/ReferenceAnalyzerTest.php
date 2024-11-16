<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tests\Unit;

use BaseCodeOy\Analyzer\ReferenceAnalyzer;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class ReferenceAnalyzerTest extends TestCase
{
    public function test_can_generate_refs(): void
    {
        $refs = (new ReferenceAnalyzer())->analyze(__FILE__);

        self::assertSame([
            ReferenceAnalyzer::class,
            TestCase::class,
            \PhpParser\NodeTraverser::class,
            \PhpParser\NodeVisitor\NameResolver::class,
            \PhpParser\Parser::class,
            \PhpParser\ParserFactory::class,
            \BaseCodeOy\Analyzer\ImportVisitor::class,
            \BaseCodeOy\Analyzer\NameVisitor::class,
            \BaseCodeOy\Analyzer\DocVisitor::class,
            \BaseCodeOy\Analyzer\DocProcessor::class,
        ], $refs);
    }

    public function test_can_generate_more_refs(): void
    {
        $refs = (new ReferenceAnalyzer())->analyze(__DIR__.'/../../src/ReferenceAnalyzer.php');

        self::assertSame([
            \PhpParser\NodeTraverser::class,
            \PhpParser\NodeVisitor\NameResolver::class,
            \PhpParser\Parser::class,
            \PhpParser\ParserFactory::class,
            \BaseCodeOy\Analyzer\ImportVisitor::class,
            \BaseCodeOy\Analyzer\NameVisitor::class,
            \BaseCodeOy\Analyzer\DocVisitor::class,
            \BaseCodeOy\Analyzer\DocProcessor::class,
        ], $refs);
    }

    public function test_can_generate_using_func_stub(): void
    {
        $refs = (new ReferenceAnalyzer())->analyze(__DIR__.'/stubs/func.php');

        self::assertSame([], $refs);
    }

    public function test_can_generate_using_bool_stub(): void
    {
        $refs = (new ReferenceAnalyzer())->analyze(__DIR__.'/stubs/bool.php');

        self::assertSame([], $refs);
    }

    public function test_can_generate_using_eg_stub(): void
    {
        $refs = (new ReferenceAnalyzer())->analyze(__DIR__.'/stubs/eg.php');

        self::assertSame(['Foo\\Baz'], $refs);
    }
}
