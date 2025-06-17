<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

namespace Fisharebest\Webtrees\CommonMark;

use League\CommonMark\Extension\Table\Table;
use League\CommonMark\Extension\Table\TableCell;
use League\CommonMark\Extension\Table\TableRow;
use League\CommonMark\Extension\Table\TableSection;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Cursor;

use function array_map;
use function explode;
use function str_starts_with;
use function strlen;
use function substr;

/**
 * Convert webtrees 1.x census-assistant markup into tables.
 * Note that webtrees 2.0 generates markdown tables directly.
 */
class CensusTableContinueParser extends AbstractBlockContinueParser
{
    private Table $table;

    private TableSection $thead;

    private TableSection $tbody;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->table = new Table();
        $this->thead = new TableSection(TableSection::TYPE_HEAD);
        $this->tbody = new TableSection(TableSection::TYPE_BODY);
        $this->table->appendChild($this->thead);
        $this->table->appendChild($this->tbody);
    }

    /**
     * @param Cursor                       $cursor
     * @param BlockContinueParserInterface $activeBlockParser
     *
     * @return BlockContinue|null
     */
    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): BlockContinue|null
    {
        $line = $cursor->getLine();

        if ($line === CensusTableExtension::CA_SUFFIX) {
            return BlockContinue::finished();
        }

        // Blank line before the suffix is an error.
        if ($line === '') {
            return BlockContinue::none();
        }

        $cells = explode('|', $line);

        $callback = static function (string $text): string {
            if (str_starts_with($text, CensusTableExtension::TH_PREFIX)) {
                return substr($text, strlen(CensusTableExtension::TH_PREFIX));
            }

            return $text;
        };

        $tr = new TableRow();

        if (empty($this->thead->children())) {
            $cells = array_map($callback, $cells);

            foreach ($cells as $cell) {
                $table_cell = new TableCell(TableCell::TYPE_HEADER);
                $table_cell->appendChild(new Text($cell));
                $tr->appendChild($table_cell);
            }

            $this->thead->appendChild($tr);
        } else {
            foreach ($cells as $cell) {
                $table_cell = new TableCell(TableCell::TYPE_DATA);
                $table_cell->appendChild(new Text($cell));
                $tr->appendChild($table_cell);
            }

            $this->tbody->appendChild($tr);
        }

        return BlockContinue::at($cursor);
    }

    /**
     * @return Table
     */
    public function getBlock(): AbstractBlock
    {
        return $this->table;
    }
}
