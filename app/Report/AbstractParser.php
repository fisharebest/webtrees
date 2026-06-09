<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Report;

use Closure;
use DOMNode;
use LogicException;
use RuntimeException;
use XMLReader;

use function libxml_clear_errors;
use function libxml_get_errors;
use function libxml_use_internal_errors;
use function sprintf;
use function trim;

abstract class AbstractParser
{
    /** The reader currently driving dispatch.  Sub-fragment parses push a fresh reader on top of this one. */
    protected XMLReader $xml_reader;

    /** Character data accumulator for the current text-bearing element. */
    protected string $text = '';

    /** @var array<string,Closure(array<string,string>):void> */
    protected array $start_handlers;

    /** @var array<string,Closure():void> */
    protected array $end_handlers;

    public function __construct(
        protected string $report,
    ) {
        $this->start_handlers = $this->startHandlers();
        $this->end_handlers   = $this->endHandlers();

        $reader = XMLReader::open($report);

        if ($reader === false) {
            throw new RuntimeException(sprintf('Cannot open report XML file: %s', $report));
        }

        $this->xml_reader = $reader;

        try {
            $this->parse();
        } finally {
            $this->xml_reader->close();
        }
    }

    /**
     * Build the dispatch table for XML start tags.  Keys are XML element
     * names (case-sensitive, matching the case used in the report files);
     * values are closures that receive the element's attributes.
     *
     * @return array<string,Closure(array<string,string>):void>
     */
    abstract protected function startHandlers(): array;

    /**
     * Build the dispatch table for XML end tags.  See {@see startHandlers()}.
     *
     * @return array<string,Closure():void>
     */
    abstract protected function endHandlers(): array;

    /**
     * Parse an in-memory XML fragment with the same handler tables as the
     * main document.  Used by ParserGenerate to evaluate the body of
     * <RepeatTag>, <Facts>, <List> and <Relatives> once per iteration.
     *
     * The current reader is pushed aside for the duration of the call so
     * that handlers calling {@see currentLineNumber()} report against the
     * fragment they are actually inside.
     */
    protected function parseFragment(string $xml): void
    {
        $sub_reader = XMLReader::XML($xml);

        if ($sub_reader === false) {
            throw new RuntimeException('Cannot create XMLReader for fragment');
        }

        $previous_reader  = $this->xml_reader;
        $this->xml_reader = $sub_reader;

        try {
            $this->parse();
        } finally {
            $sub_reader->close();
            $this->xml_reader = $previous_reader;
        }
    }

    /**
     * Drive the pull-parser loop on the current reader, dispatching each
     * node to the appropriate handler.  libxml errors are routed through
     * the internal queue so we can convert them into exceptions instead of
     * leaking PHP warnings.
     */
    protected function parse(): void
    {
        $previous_use_errors = libxml_use_internal_errors(true);
        libxml_clear_errors();

        try {
            while ($this->xml_reader->read()) {
                $this->dispatchNode();
            }

            $errors = libxml_get_errors();

            if ($errors !== []) {
                $first = $errors[0];

                throw new LogicException(sprintf(
                    'XML error in report %s: %s at line %d',
                    $this->report,
                    trim($first->message),
                    $first->line
                ));
            }
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous_use_errors);
        }
    }

    /**
     * Dispatch the reader's current node.  Empty elements (<foo/>) are
     * synthesised into a start/end pair so handler tables can stay
     * symmetric.
     */
    protected function dispatchNode(): void
    {
        $reader = $this->xml_reader;

        switch ($reader->nodeType) {
            case XMLReader::ELEMENT:
                $name     = $reader->name;
                $is_empty = $reader->isEmptyElement;
                $attrs    = $this->readAttributes();

                $this->startElement($name, $attrs);

                if ($is_empty) {
                    $this->endElement($name);
                }
                break;

            case XMLReader::END_ELEMENT:
                $this->endElement($reader->name);
                break;

            case XMLReader::TEXT:
            case XMLReader::CDATA:
            case XMLReader::SIGNIFICANT_WHITESPACE:
                $this->characterData($reader->value);
                break;
        }
    }

    /**
     * Read every attribute of the element the reader is positioned on.
     * After collecting them we move back to the element itself so the
     * caller can still query element-level state (isEmptyElement, etc.).
     *
     * @return array<string,string>
     */
    protected function readAttributes(): array
    {
        $attrs = [];

        if ($this->xml_reader->hasAttributes && $this->xml_reader->moveToFirstAttribute()) {
            do {
                $attrs[$this->xml_reader->name] = $this->xml_reader->value;
            } while ($this->xml_reader->moveToNextAttribute());

            $this->xml_reader->moveToElement();
        }

        return $attrs;
    }

    /**
     * @param array<string,string> $attrs
     */
    protected function startElement(string $name, array $attrs): void
    {
        $handler = $this->start_handlers[$name] ?? null;

        if ($handler === null) {
            throw new LogicException(sprintf(
                'Unknown XML element <%s> in report %s on line %d',
                $name,
                $this->report,
                $this->currentLineNumber()
            ));
        }

        $handler($attrs);
    }

    protected function endElement(string $name): void
    {
        // Invalid tags are caught by the startElement() handler.
        // The parser will catch mismatched tags.
        // Therefore, we will always have an end-handler for this tag.
        $this->end_handlers[$name]();
    }

    protected function characterData(string $data): void
    {
        $this->text .= $data;
    }

    /**
     * Best-effort line number of the current reader position, used for
     * error messages.  XMLReader does not expose this directly; expand()
     * materialises the current node as a DOM node which carries the line
     * information from libxml.
     */
    protected function currentLineNumber(): int
    {
        $node = @$this->xml_reader->expand();

        if ($node instanceof DOMNode) {
            return $node->getLineNo();
        }

        return 0;
    }

    protected function noop(): void
    {
    }
}
