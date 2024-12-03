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
        $classInspector = ClassInspector::inspect(self::class);

        $this->assertInstanceOf(ClassInspector::class, $classInspector);

        $this->assertTrue($classInspector->isClass());
        $this->assertFalse($classInspector->isInterface());
        $this->assertFalse($classInspector->isTrait());
        $this->assertTrue($classInspector->exists());

        $this->assertSame([
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
        ], $classInspector->references());
    }

    public function test_can_inspect_interfaces(): void
    {
        $classInspector = ClassInspector::inspect(NodeTraverserInterface::class);

        $this->assertInstanceOf(ClassInspector::class, $classInspector);

        $this->assertFalse($classInspector->isClass());
        $this->assertTrue($classInspector->isInterface());
        $this->assertFalse($classInspector->isTrait());
        $this->assertTrue($classInspector->exists());

        $this->assertSame([\PhpParser\NodeVisitor::class, \PhpParser\Node::class], $classInspector->references());
    }

    public function test_can_inspect_traits(): void
    {
        $classInspector = ClassInspector::inspect(AnalysisTrait::class);

        $this->assertInstanceOf(ClassInspector::class, $classInspector);

        $this->assertFalse($classInspector->isClass());
        $this->assertFalse($classInspector->isInterface());
        $this->assertTrue($classInspector->isTrait());
        $this->assertTrue($classInspector->exists());

        $this->assertSame([
            \AppendIterator::class,
            \RecursiveIteratorIterator::class,
            \RecursiveDirectoryIterator::class,
            \CallbackFilterIterator::class,
            \BaseCodeOy\Analyzer\ReferenceAnalyzer::class,
            ClassInspector::class,
            \SplFileInfo::class,
        ], $classInspector->references());
    }

    public function test_can_inspect_nothing(): void
    {
        $classInspector = ClassInspector::inspect('foobarbaz');

        $this->assertInstanceOf(ClassInspector::class, $classInspector);

        $this->assertFalse($classInspector->isClass());
        $this->assertFalse($classInspector->isInterface());
        $this->assertFalse($classInspector->isTrait());
        $this->assertFalse($classInspector->exists());

        $this->assertSame([], $classInspector->references());
    }

    public function test_can_not_inspect_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The class name must be non-empty.');
        ClassInspector::inspect('');
    }
}
