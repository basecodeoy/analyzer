<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace BaseCodeOy\Analyzer;

/**
 * This is the class inspector class.
 */
final readonly class ClassInspector
{
    /**
     * Create a new class inspector instance.
     */
    private function __construct(
        /**
         * The class name.
         *
         * @var string
         */
        private string $class,
    ) {}

    /**
     * Inspect the given class.
     *
     * @throws \InvalidArgumentException
     */
    public static function inspect(string $class): self
    {
        if ($class === '' || $class === '0') {
            throw new \InvalidArgumentException('The class name must be non-empty.');
        }

        return new self($class);
    }

    /**
     * Is the class a valid class?
     */
    public function isClass(): bool
    {
        return \class_exists($this->class);
    }

    /**
     * Is the class a valid interface?
     */
    public function isInterface(): bool
    {
        return \interface_exists($this->class);
    }

    /**
     * Is the class a valid trait?
     */
    public function isTrait(): bool
    {
        return \trait_exists($this->class);
    }

    /**
     * Does the class exist?
     */
    public function exists(): bool
    {
        if ($this->isClass()) {
            return true;
        }

        if ($this->isInterface()) {
            return true;
        }

        return $this->isTrait();
    }

    /**
     * Get the native refector.
     */
    public function refector(): ?\ReflectionClass
    {
        if (!$this->exists()) {
            return null;
        }

        return new \ReflectionClass($this->class);
    }

    /**
     * Get the fully-qualified imports and type-hints.
     *
     * @return array<string>
     */
    public function references(): array
    {
        if (($refector = $this->refector()) instanceof \ReflectionClass) {
            return (new ReferenceAnalyzer())->analyze($refector->getFileName());
        }

        return [];
    }
}
