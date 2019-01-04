<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\Block\Parser\AbstractBlockParser;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;
use Webuni\CommonMark\TableExtension\Table;
use Webuni\CommonMark\TableExtension\TableCell;
use Webuni\CommonMark\TableExtension\TableRow;

/**
 * Convert webtrees 1.x census-assistant markup into tables.
 * Note that webtrees 2.0 generates markdown tables directly.
 *
 * Based on the table parser from webuni/commonmark-table-extension.
 */
class CensusTableParser extends AbstractBlockParser
{
    private const REGEX_CENSUS_TABLE_HEADER = '/^\.b\.[^.|]+(?:\|\.b\.[^.|]+)*$/';

    /**
     * Parse a paragraph of text with the following stucture:
     *
     * .b.HEADING1|.b.HEADING2|.b.HEADING3
     * COL1|COL2|COL3
     * COL1|COL2|COL3
     *
     * @param ContextInterface $context
     * @param Cursor           $cursor
     *
     * @return bool
     */
    public function parse(ContextInterface $context, Cursor $cursor): bool
    {
        $container = $context->getContainer();

        // Replace paragraphs with tables
        if (!$container instanceof Paragraph) {
            return false;
        }

        $lines = $container->getStrings();

        $first_line = array_pop($lines);

        if (!preg_match(self::REGEX_CENSUS_TABLE_HEADER, $first_line)) {
            return false;
        }

        $head = $this->parseRow($first_line, TableCell::TYPE_HEAD);

        $table = new Table(function (Cursor $cursor) use (&$table): bool {
            $row = $this->parseRow($cursor->getLine(), TableCell::TYPE_BODY);
            if ($row === null) {
                return false;
            }
            $table->getBody()->appendChild($row);

            return true;
        });

        $table->getHead()->appendChild($head);
        $context->replaceContainerBlock($table);

        return true;
    }

    /**
     * @param string $line
     * @param string $type
     *
     * @return TableRow|null
     */
    private function parseRow($line, $type)
    {
        if (strpos($line, '|') === false) {
            return null;
        }

        $row = new TableRow();
        foreach (explode('|', $line) as $cell) {
            // Strip leading ".b." from <th> cells
            if ($type === TableCell::TYPE_HEAD && substr_compare($cell, '.b.', 0)) {
                $cell = substr($cell, 3);
            }
            $row->appendChild(new TableCell($cell, $type, null));
        }

        return $row;
    }
}
