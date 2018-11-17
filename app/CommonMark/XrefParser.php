<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\CommonMark;

use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Tree;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Parser\AbstractInlineParser;
use League\CommonMark\InlineParserContext;

/**
 * Convert XREFs within markdown text to links
 */
class XrefParser extends AbstractInlineParser
{
    /** @var Tree - match XREFs in this tree */
    private $tree;

    /**
     * MarkdownXrefParser constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    /**
     * We are only interested in text that begins with '@'.
     *
     * @return string[]
     */
    public function getCharacters(): array
    {
        return ['@'];
    }

    /**
     * @param InlineParserContext $context
     *
     * @return bool
     */
    public function parse(InlineParserContext $context): bool
    {
        // The cursor should be positioned on the opening '@'.
        $cursor = $context->getCursor();

        // If this isn't the start of an XREF, we'll need to rewind.
        $previous_state = $cursor->saveState();

        $handle = $cursor->match('/@' . Gedcom::REGEX_XREF . '@/');
        if (empty($handle)) {
            // Not an XREF?
            $cursor->restoreState($previous_state);

            return false;
        }

        $xref = trim($handle, '@');

        $record = GedcomRecord::getInstance($xref, $this->tree);

        if ($record === null) {
            // Linked record does not exist?
            $cursor->restoreState($previous_state);

            return false;
        }

        $url   = $record->url();
        $label = $handle;
        $title = strip_tags($record->getFullName());
        $context->getContainer()->appendChild(new Link($url, $label, $title));

        return true;
    }
}
