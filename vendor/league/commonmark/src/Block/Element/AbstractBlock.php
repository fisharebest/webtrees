<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Block\Element;

use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;
use League\CommonMark\Node\Node;

/**
 * Block-level element
 */
abstract class AbstractBlock extends Node
{
    /**
     * Used for storage of arbitrary data.
     *
     * @var array
     */
    public $data = [];

    /**
     * @var bool
     */
    protected $open = true;

    /**
     * @var bool
     */
    protected $lastLineBlank = false;

    /**
     * @var int
     */
    protected $startLine;

    /**
     * @var int
     */
    protected $endLine;

    /**
     * @param Node|null $node
     */
    protected function setParent(Node $node = null)
    {
        if ($node && !$node instanceof self) {
            throw new \InvalidArgumentException('Parent of block must also be block (can not be inline)');
        }

        parent::setParent($node);
    }

    /**
     * @return bool
     */
    public function isContainer(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function hasChildren(): bool
    {
        return $this->firstChild !== null;
    }

    /**
     * Returns true if this block can contain the given block as a child node
     *
     * @param AbstractBlock $block
     *
     * @return bool
     */
    abstract public function canContain(AbstractBlock $block): bool;

    /**
     * Whether this is a code block
     *
     * @return bool
     */
    abstract public function isCode(): bool;

    /**
     * @param Cursor $cursor
     *
     * @return bool
     */
    abstract public function matchesNextLine(Cursor $cursor): bool;

    /**
     * @param int $startLine
     *
     * @return $this
     */
    public function setStartLine(int $startLine)
    {
        $this->startLine = $startLine;
        if (empty($this->endLine)) {
            $this->endLine = $startLine;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getStartLine(): int
    {
        return $this->startLine;
    }

    /**
     * @param int $endLine
     *
     * @return $this
     */
    public function setEndLine(int $endLine)
    {
        $this->endLine = $endLine;

        return $this;
    }

    /**
     * @return int
     */
    public function getEndLine(): int
    {
        return $this->endLine;
    }

    /**
     * Whether the block ends with a blank line
     *
     * @return bool
     */
    public function endsWithBlankLine(): bool
    {
        return $this->lastLineBlank;
    }

    /**
     * @param bool $blank
     */
    public function setLastLineBlank(bool $blank)
    {
        $this->lastLineBlank = $blank;
    }

    /**
     * Determines whether the last line should be marked as blank
     *
     * @param Cursor $cursor
     * @param int    $currentLineNumber
     *
     * @return bool
     */
    public function shouldLastLineBeBlank(Cursor $cursor, int $currentLineNumber): bool
    {
        return $cursor->isBlank();
    }

    /**
     * Whether the block is open for modifications
     *
     * @return bool
     */
    public function isOpen(): bool
    {
        return $this->open;
    }

    /**
     * Finalize the block; mark it closed for modification
     *
     * @param ContextInterface $context
     * @param int              $endLineNumber
     */
    public function finalize(ContextInterface $context, int $endLineNumber)
    {
        if (!$this->open) {
            return;
        }

        $this->open = false;
        $this->endLine = $endLineNumber;

        $context->setTip($context->getTip()->parent());
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getData(string $key, $default = null)
    {
        return \array_key_exists($key, $this->data) ? $this->data[$key] : $default;
    }
}
