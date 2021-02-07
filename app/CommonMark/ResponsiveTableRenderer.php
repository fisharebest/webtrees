<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\Extension\Table\TableRenderer;
use League\CommonMark\HtmlElement;

/**
 * Class ResponsiveTableRenderer - wrap markdown tables in a responsive DIV element.
 *
 * @package Fisharebest\Webtrees\CommonMark
 */
class ResponsiveTableRenderer implements BlockRendererInterface
{
    // A table is made responsive by wrapping it in a DIV element.
    private const WRAP_ELEMENT    = 'div';
    private const WRAP_ATTRIBUTES = ['class' => 'table-responsive'];

    /** @var TableRenderer */
    private $table_renderer;

    /**
     * ResponsiveTableRenderer constructor.
     */
    public function __construct()
    {
        $this->table_renderer = new TableRenderer();
    }

    /**
     * @param AbstractBlock            $block
     * @param ElementRendererInterface $htmlRenderer
     * @param bool                     $inTightList
     *
     * @return HtmlElement
     */
    public function render(
        AbstractBlock $block,
        ElementRendererInterface $htmlRenderer,
        bool $inTightList = false
    ): HtmlElement {
        $table_element = $this->table_renderer->render($block, $htmlRenderer, $inTightList);

        return new HtmlElement(self::WRAP_ELEMENT, self::WRAP_ATTRIBUTES, $table_element);
    }
}
