<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use League\CommonMark\Inline\Parser\InlineParserInterface;
use League\CommonMark\InlineParserContext;

use function is_string;
use function trim;

/**
 * Convert XREFs within markdown text to links
 */
class XrefParser implements InlineParserInterface
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

        // If this isn't the start of an XREF, we'll need to rewind to here.
        $previous_state = $cursor->saveState();

        $xref = $cursor->match('/@' . Gedcom::REGEX_XREF . '@/');

        if (is_string($xref)) {
            $xref   = trim($xref, '@');
            $record = Registry::gedcomRecordFactory()->make($xref, $this->tree);

            if ($record instanceof GedcomRecord) {
                $context->getContainer()->appendChild(new XrefNode($record));

                return true;
            }
        }

        // Not an XREF? Linked record does not exist?
        $cursor->restoreState($previous_state);

        return false;
    }
}
