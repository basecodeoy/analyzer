<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace BaseCodeOy\Analyzer;

use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\AbstractList;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Nullable;
use phpDocumentor\Reflection\Types\Object_;

/**
 * This is the doc processor class.
 */
final class DocProcessor
{
    /**
     * Process an array of phpdoc.
     *
     * Returns FQCN strings for each reference.
     *
     * @param  array<\phpDocumentor\Reflection\DocBlock> $docs
     * @return array<string>
     */
    public static function process(array $docs): array
    {
        return self::flatmap(fn ($doc): array => self::flatmap(fn ($tag): array => self::flatmap(fn ($type): array => self::processType($type), self::processTag($tag)), $doc->getTags()), $docs);
    }

    /**
     * Apply the function and flatten the result.
     */
    private static function flatmap(callable $fn, array $array): array
    {
        if ($array === []) {
            return [];
        }

        return \array_merge(...\array_map($fn, $array));
    }

    /**
     * Process a tag into types.
     *
     * @param  BaseTag|\phpDocumentor\Reflection\DocBlock\Tags\InvalidTag $tag
     * @return array<Type>
     */
    private static function processTag(object $tag): array
    {
        if (!$tag instanceof BaseTag) {
            return [];
        }

        $types = [];

        if (\method_exists($tag, 'getType') && \is_callable([$tag, 'getType']) && ($type = $tag->getType()) !== null) {
            $types[] = $type;
        }

        if (\method_exists($tag, 'getArguments') && \is_callable([$tag, 'getArguments'])) {
            foreach ($tag->getArguments() as $argument) {
                if (($type = $argument['type'] ?? null) !== null) {
                    $types[] = $type;
                }
            }
        }

        return $types;
    }

    /**
     * Process a type into FQCN strings.
     *
     * @return array<string>
     */
    private static function processType(Type $type): array
    {
        if ($type instanceof AbstractList) {
            return self::flatmap(fn ($t): array => self::processType($t), [$type->getKeyType(), $type->getValueType()]);
        }

        if ($type instanceof Compound) {
            return self::flatmap(fn ($t): array => self::processType($t), \iterator_to_array($type));
        }

        if ($type instanceof Nullable) {
            return self::processType($type->getActualType());
        }

        if ($type instanceof Object_ && ($fq = $type->getFqsen()) instanceof \phpDocumentor\Reflection\Fqsen) {
            return [\ltrim((string) $fq, '\\')];
        }

        return [];
    }
}
