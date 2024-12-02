<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace BaseCodeOy\Analyzer;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\ContextFactory;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeVisitorAbstract;

/**
 * This is the doc visitor class.
 */
final class DocVisitor extends NodeVisitorAbstract
{
    /**
     * The current context.
     */
    private ?Context $context = null;

    /**
     * The recorded phpdoc.
     *
     * @var null|array<DocBlock>
     */
    private ?array $doc = null;

    /**
     * Create a new doc visitor instance.
     */
    public function __construct(
        /**
         * The context factory.
         *
         * @var \Closure(string): Context
         */
        private readonly \Closure $contextFactory,
        /**
         * The phpdoc factory.
         *
         * @var \Closure(string, Context): DocBlock
         */
        private readonly \Closure $phpdocFactory,
    ) {}

    /**
     * Create a new doc visitor aware of file contents.
     */
    public static function create(string $contents): self
    {
        $contextInst = new ContextFactory();

        $context = fn (string $namespace): Context => $contextInst->createForNamespace($namespace, $contents);

        $phpdocInst = DocBlockFactory::createInstance();

        $phpdoc = fn (string $doc, Context $context): DocBlock => $phpdocInst->create($doc, $context);

        return new self($context, $phpdoc);
    }

    /**
     * Reset the recorded imports.
     *
     * @param array<Node> $nodes
     */
    #[\Override()]
    public function beforeTraverse(array $nodes): void
    {
        $this->resetContext();
        $this->doc = [];
    }

    /**
     * Enter the node and record the phpdoc.
     */
    #[\Override()]
    public function enterNode(Node $node): Node
    {
        if ($node instanceof Namespace_) {
            $this->resetContext($node->name);
        }

        $this->recordDoc($node->getAttribute('comments', []));

        return $node;
    }

    /**
     * Get the recorded phpdoc.
     *
     * Returns null if not traversed yet.
     *
     * @return null|array<DocBlock>
     */
    public function getDoc(): ?array
    {
        return $this->doc;
    }

    /**
     * Reset the visitor context.
     */
    private function resetContext(?Name $namespace = null): void
    {
        $callable = $this->contextFactory;

        $this->context = $callable($namespace instanceof Name ? $namespace->toString() : '');
    }

    /**
     * Reset the visitor context.
     *
     * @param array<\PhpParser\Comment> $comments
     */
    private function recordDoc(array $comments): void
    {
        $callable = $this->phpdocFactory;

        foreach ($comments as $comment) {
            if ($comment instanceof Doc) {
                $this->doc[] = $callable($comment->getText(), $this->context);
            }
        }
    }
}
