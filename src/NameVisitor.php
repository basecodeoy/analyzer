<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace BaseCodeOy\Analyzer;

use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\NodeVisitorAbstract;

/**
 * This is the name visitor class.
 */
final class NameVisitor extends NodeVisitorAbstract
{
    /**
     * The recorded names.
     *
     * @var null|array<string>
     */
    private ?array $names = null;

    /**
     * Reset the recorded names.
     *
     * @param array<Node> $nodes
     */
    #[\Override()]
    public function beforeTraverse(array $nodes): void
    {
        $this->names = [];
    }

    /**
     * Enter the node and record the name.
     */
    #[\Override()]
    public function enterNode(Node $node): Node
    {
        if ($node instanceof ConstFetch || $node instanceof FuncCall) {
            $node->name->name = 'PURGE';
        }

        if ($node instanceof FullyQualified && $node->toString() !== 'PURGE') {
            $this->names[] = $node->toString();
        }

        return $node;
    }

    /**
     * Get the recorded names.
     *
     * Returns null if not traversed yet.
     *
     * @return null|array<string>
     */
    public function getNames(): ?array
    {
        return $this->names;
    }
}
