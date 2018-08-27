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
namespace Fisharebest\Webtrees\CommonMark;

use League\CommonMark\Block\Parser\BlockParserInterface;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\Extension\Extension;
use Webuni\CommonMark\TableExtension\Table;
use Webuni\CommonMark\TableExtension\TableCell;
use Webuni\CommonMark\TableExtension\TableCellRenderer;
use Webuni\CommonMark\TableExtension\TableRenderer;
use Webuni\CommonMark\TableExtension\TableRow;
use Webuni\CommonMark\TableExtension\TableRowRenderer;
use Webuni\CommonMark\TableExtension\TableRows;
use Webuni\CommonMark\TableExtension\TableRowsRenderer;

/**
 * Convert webtrees 1.x census-assistant markup into tables.
 * Note that webtrees 2.0 generates markdown tables directly.
 *
 * Based on the table parser from webuni/commonmark-table-extension.
 */
class CensusTableExtension extends Extension
{
    /**
     * Returns a list of block parsers to add to the existing list
     *
     * @return BlockParserInterface[]
     */
    public function getBlockParsers(): array
    {
        return [
            new CensusTableParser(),
        ];
    }

    /**
     * Returns a list of block renderers to add to the existing list
     *
     * The list keys are the block class names which the corresponding value (renderer) will handle.
     *
     * @return BlockRendererInterface[]
     */
    public function getBlockRenderers(): array
    {
        return [
            Table::class     => new TableRenderer(),
            TableRows::class => new TableRowsRenderer(),
            TableRow::class  => new TableRowRenderer(),
            TableCell::class => new TableCellRenderer(),
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'censustabletable';
    }
}
