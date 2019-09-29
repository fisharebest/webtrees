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

use League\CommonMark\ConfigurableEnvironmentInterface;
use League\CommonMark\Ext\Table\Table;
use League\CommonMark\Ext\Table\TableCell;
use League\CommonMark\Ext\Table\TableCellRenderer;
use League\CommonMark\Ext\Table\TableRenderer;
use League\CommonMark\Ext\Table\TableRow;
use League\CommonMark\Ext\Table\TableRowRenderer;
use League\CommonMark\Ext\Table\TableSection;
use League\CommonMark\Ext\Table\TableSectionRenderer;
use League\CommonMark\Extension\ExtensionInterface;

/**
 * Convert webtrees 1.x census-assistant markup into tables.
 * Note that webtrees 2.0 generates markdown tables directly.
 *
 * Based on the table parser from league/commonmark-ext-table.
 */
class CensusTableExtension implements ExtensionInterface
{
    public function register(ConfigurableEnvironmentInterface $environment): void
    {
        $environment
            ->addBlockParser(new CensusTableParser())
            ->addBlockRenderer(Table::class, new TableRenderer())
            ->addBlockRenderer(TableSection::class, new TableSectionRenderer())
            ->addBlockRenderer(TableRow::class, new TableRowRenderer())
            ->addBlockRenderer(TableCell::class, new TableCellRenderer());
    }
}
