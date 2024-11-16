<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace BaseCodeOy\Analyzer;

use PhpParser\Node;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeVisitorAbstract;

/**
 * This is the import visitor class.
 */
final class ImportVisitor extends NodeVisitorAbstract
{
    /**
     * The recorded imports.
     *
     * @var null|array<string>
     */
    private ?array $imports = null;

    /**
     * Reset the recorded imports.
     *
     * @param array<\PhpParser\Node> $nodes
     */
    #[\Override()]
    public function beforeTraverse(array $nodes): void
    {
        $this->imports = [];
    }

    /**
     * Enter the node and record the import.
     */
    #[\Override()]
    public function enterNode(Node $node): Node
    {
        if ($node instanceof UseUse) {
            $this->imports[] = $node->name->toString();
        }

        return $node;
    }

    /**
     * Get the recorded imports.
     *
     * Returns null if not traversed yet.
     *
     * @return null|array<string>
     */
    public function getImports(): ?array
    {
        return $this->imports;
    }
}
