<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace BaseCodeOy\Analyzer;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;

/**
 * This is the reference analyzer class.
 */
final readonly class ReferenceAnalyzer
{
    /**
     * The parser instance.
     */
    private Parser $parser;

    /**
     * Create a new reference analyzer instance.
     */
    public function __construct(?Parser $parser = null)
    {
        $this->parser = $parser ?: (new ParserFactory())->createForHostVersion();
    }

    /**
     * Get the fully-qualified imports and type-hints.
     *
     * @return array<string>
     */
    public function analyze(string $path): array
    {
        $contents = (string) \file_get_contents($path);

        $traverser = new NodeTraverser();

        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor($imports = new ImportVisitor());
        $traverser->addVisitor($names = new NameVisitor());
        $traverser->addVisitor($docs = DocVisitor::create($contents));

        $traverser->traverse($this->parser->parse($contents));

        return \array_values(\array_unique(\array_merge(
            $imports->getImports(),
            $names->getNames(),
            DocProcessor::process($docs->getDoc()),
        )));
    }
}
