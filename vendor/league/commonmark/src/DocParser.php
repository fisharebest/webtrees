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

namespace League\CommonMark;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\InlineContainer;
use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\Node\NodeWalker;

class DocParser
{
    /**
     * @var Environment
     */
    protected $environment;

    /**
     * @var InlineParserEngine
     */
    private $inlineParserEngine;

    /**
     * @param Environment $environment
     */
    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
        $this->inlineParserEngine = new InlineParserEngine($environment);
    }

    /**
     * @return Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param string $input
     *
     * @return string[]
     */
    private function preProcessInput($input)
    {
        $lines = preg_split('/\r\n|\n|\r/', $input);

        // Remove any newline which appears at the very end of the string.
        // We've already split the document by newlines, so we can simply drop
        // any empty element which appears on the end.
        if (end($lines) === '') {
            array_pop($lines);
        }

        return $lines;
    }

    /**
     * @param string $input
     *
     * @return Document
     */
    public function parse($input)
    {
        $context = new Context(new Document(), $this->getEnvironment());

        $lines = $this->preProcessInput($input);
        foreach ($lines as $line) {
            $context->setNextLine($line);
            $this->incorporateLine($context);
        }

        while ($context->getTip()) {
            $context->getTip()->finalize($context, count($lines));
        }

        $this->processInlines($context, $context->getDocument()->walker());

        $this->processDocument($context);

        return $context->getDocument();
    }

    private function incorporateLine(ContextInterface $context)
    {
        $cursor = new Cursor($context->getLine());
        $context->getBlockCloser()->resetTip();

        $context->setBlocksParsed(false);

        $this->resetContainer($context, $cursor);
        $context->getBlockCloser()->setLastMatchedContainer($context->getContainer());

        $this->parseBlocks($context, $cursor);

        // What remains at the offset is a text line.  Add the text to the appropriate container.
        // First check for a lazy paragraph continuation:
        if ($this->isLazyParagraphContinuation($context, $cursor)) {
            // lazy paragraph continuation
            $context->getTip()->addLine($cursor->getRemainder());

            return;
        }

        // not a lazy continuation
        // finalize any blocks not matched
        $context->getBlockCloser()->closeUnmatchedBlocks();

        // Determine whether the last line is blank, updating parents as needed
        $this->setAndPropagateLastLineBlank($context, $cursor);

        // Handle any remaining cursor contents
        if ($context->getContainer()->acceptsLines()) {
            $context->getContainer()->handleRemainingContents($context, $cursor);
        } elseif (!$cursor->isBlank()) {
            // Create paragraph container for line
            $context->addBlock(new Paragraph());
            $cursor->advanceToFirstNonSpace();
            $context->getTip()->addLine($cursor->getRemainder());
        }
    }

    private function processDocument(ContextInterface $context)
    {
        foreach ($this->getEnvironment()->getDocumentProcessors() as $documentProcessor) {
            $documentProcessor->processDocument($context->getDocument());
        }
    }

    private function processInlines(ContextInterface $context, NodeWalker $walker)
    {
        while (($event = $walker->next()) !== null) {
            if (!$event->isEntering()) {
                continue;
            }

            $node = $event->getNode();
            if ($node instanceof InlineContainer) {
                $this->inlineParserEngine->parse($node, $context->getDocument()->getReferenceMap());
            }
        }
    }

    /**
     * Sets the container to the last open child (or its parent)
     *
     * @param ContextInterface $context
     * @param Cursor           $cursor
     */
    private function resetContainer(ContextInterface $context, Cursor $cursor)
    {
        $context->setContainer($context->getDocument());

        while ($context->getContainer()->hasChildren()) {
            $lastChild = $context->getContainer()->lastChild();
            if (!$lastChild->isOpen()) {
                break;
            }

            $context->setContainer($lastChild);
            if (!$context->getContainer()->matchesNextLine($cursor)) {
                $context->setContainer($context->getContainer()->parent()); // back up to the last matching block
                break;
            }
        }
    }

    /**
     * Parse blocks
     *
     * @param ContextInterface $context
     * @param Cursor           $cursor
     */
    private function parseBlocks(ContextInterface $context, Cursor $cursor)
    {
        while (!$context->getContainer()->isCode() && !$context->getBlocksParsed()) {
            $parsed = false;
            foreach ($this->environment->getBlockParsers() as $parser) {
                if ($parser->parse($context, $cursor)) {
                    $parsed = true;
                    break;
                }
            }

            if (!$parsed || $context->getContainer()->acceptsLines()) {
                $context->setBlocksParsed(true);
            }
        }
    }

    /**
     * @param ContextInterface $context
     * @param Cursor           $cursor
     *
     * @return bool
     */
    private function isLazyParagraphContinuation(ContextInterface $context, Cursor $cursor)
    {
        return !$context->getBlockCloser()->areAllClosed() &&
            !$cursor->isBlank() &&
            $context->getTip() instanceof Paragraph &&
            count($context->getTip()->getStrings()) > 0;
    }

    /**
     * @param ContextInterface $context
     * @param Cursor           $cursor
     */
    private function setAndPropagateLastLineBlank(ContextInterface $context, $cursor)
    {
        if ($cursor->isBlank() && $lastChild = $context->getContainer()->lastChild()) {
            if ($lastChild instanceof AbstractBlock) {
                $lastChild->setLastLineBlank(true);
            }
        }

        $container = $context->getContainer();
        $lastLineBlank = $container->shouldLastLineBeBlank($cursor, $context->getLineNumber());

        // Propagate lastLineBlank up through parents:
        while ($container) {
            $container->setLastLineBlank($lastLineBlank);
            $container = $container->parent();
        }
    }
}
