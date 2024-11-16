<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tests\Unit;

use BaseCodeOy\Analyzer\AnalysisTrait;
use BaseCodeOy\Analyzer\ClassInspector;
use PhpParser\NodeTraverserInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class ClassInspectorTest extends TestCase
{
    public function test_can_inspect_classes(): void
    {
        $inspector = ClassInspector::inspect(self::class);

        self::assertInstanceOf(ClassInspector::class, $inspector);

        self::assertTrue($inspector->isClass());
        self::assertFalse($inspector->isInterface());
        self::assertFalse($inspector->isTrait());
        self::assertTrue($inspector->exists());

        self::assertSame([
            AnalysisTrait::class,
            ClassInspector::class,
            NodeTraverserInterface::class,
            TestCase::class,
            \PhpParser\NodeVisitor::class,
            \PhpParser\Node::class,
            \AppendIterator::class,
            \RecursiveIteratorIterator::class,
            \RecursiveDirectoryIterator::class,
            \CallbackFilterIterator::class,
            \BaseCodeOy\Analyzer\ReferenceAnalyzer::class,
            \SplFileInfo::class,
            \InvalidArgumentException::class,
        ], $inspector->references());
    }

    public function test_can_inspect_interfaces(): void
    {
        $inspector = ClassInspector::inspect(NodeTraverserInterface::class);

        self::assertInstanceOf(ClassInspector::class, $inspector);

        self::assertFalse($inspector->isClass());
        self::assertTrue($inspector->isInterface());
        self::assertFalse($inspector->isTrait());
        self::assertTrue($inspector->exists());

        self::assertSame([\PhpParser\NodeVisitor::class, \PhpParser\Node::class], $inspector->references());
    }

    public function test_can_inspect_traits(): void
    {
        $inspector = ClassInspector::inspect(AnalysisTrait::class);

        self::assertInstanceOf(ClassInspector::class, $inspector);

        self::assertFalse($inspector->isClass());
        self::assertFalse($inspector->isInterface());
        self::assertTrue($inspector->isTrait());
        self::assertTrue($inspector->exists());

        self::assertSame([
            \AppendIterator::class,
            \RecursiveIteratorIterator::class,
            \RecursiveDirectoryIterator::class,
            \CallbackFilterIterator::class,
            \BaseCodeOy\Analyzer\ReferenceAnalyzer::class,
            ClassInspector::class,
            \SplFileInfo::class,
        ], $inspector->references());
    }

    public function test_can_inspect_nothing(): void
    {
        $inspector = ClassInspector::inspect('foobarbaz');

        self::assertInstanceOf(ClassInspector::class, $inspector);

        self::assertFalse($inspector->isClass());
        self::assertFalse($inspector->isInterface());
        self::assertFalse($inspector->isTrait());
        self::assertFalse($inspector->exists());

        self::assertSame([], $inspector->references());
    }

    public function test_can_not_inspect_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The class name must be non-empty.');
        ClassInspector::inspect('');
    }
}
