<?php

declare(strict_types=1);

/*
 * This is part of the league/commonmark-ext-table package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 * (c) Webuni s.r.o. <info@webuni.cz>
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Ext\Table;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;
use League\CommonMark\Util\Xml;

final class TableCaptionRenderer implements BlockRendererInterface
{
    public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, bool $inTightList = false)
    {
        if (!$block instanceof TableCaption) {
            throw new \InvalidArgumentException('Incompatible block type: '.get_class($block));
        }

        $attrs = [];
        foreach ($block->getData('attributes', []) as $key => $value) {
            $attrs[$key] = Xml::escape($value);
        }

        if ($block->id) {
            $attrs['id'] = $block->id;
        }

        return new HtmlElement('caption', $attrs, $htmlRenderer->renderInlines($block->children()));
    }
}
