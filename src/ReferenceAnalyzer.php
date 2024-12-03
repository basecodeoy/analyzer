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
        $this->parser = $parser instanceof Parser ? $parser : (new ParserFactory())->createForHostVersion();
    }

    /**
     * Get the fully-qualified imports and type-hints.
     *
     * @return array<string>
     */
    public function analyze(string $path): array
    {
        $contents = (string) \file_get_contents($path);

        $nodeTraverser = new NodeTraverser();

        $nodeTraverser->addVisitor(new NameResolver());
        $nodeTraverser->addVisitor($importVisitor = new ImportVisitor());
        $nodeTraverser->addVisitor($nameVisitor = new NameVisitor());
        $nodeTraverser->addVisitor($docs = DocVisitor::create($contents));

        $nodeTraverser->traverse($this->parser->parse($contents));

        return \array_values(\array_unique(\array_merge(
            $importVisitor->getImports(),
            $nameVisitor->getNames(),
            DocProcessor::process($docs->getDoc()),
        )));
    }
}
