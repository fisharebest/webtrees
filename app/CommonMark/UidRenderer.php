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

use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use Stringable;

/**
 * Convert XREFs within markdown text to links
 */
class UidRenderer implements NodeRendererInterface
{
    /**
     * @param Node                       $node
     * @param ChildNodeRendererInterface $childRenderer
     *
     * @return Stringable
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): Stringable
    {
        UidNode::assertInstanceOf($node);

        /** @var UidNode $node */
        $href = $node->record()->url();

        if (empty($node->linkText())) {
            $html = $node->record()->fullName();
        } elseif ($node->linkText === "FULLNAME") {
            $html = $node->record()->fullName();
#TODO Needs to be checked if the record has the fact asked for
#TODO Needs take the date format defined acording to the language shown.
#TODO         } elseif ($node->linkText === "BIRT:DATE") {
#TODO             $html = $node->record()->fullName();
#TODO                         $tmp   = new Date($value);
#TODO                         $dfmt = "%j %F %Y";
#TODO                         $value = strip_tags($tmp->display(null, $dfmt));
#TODO         } elseif ($node->linkText === "BIRT:PLAC") {
#TODO             $html = $node->record()->fullName();
#TODO                         $tmp   = new Place($value, $this->tree);
#TODO                         $value = $tmp->shortName();
#TODO         } elseif ($node->linkText === "DEAT:DATE") {
#TODO             $html = $node->record()->fullName();
#TODO                         $tmp   = new Date($value);
#TODO                         $dfmt = "%j %F %Y";
#TODO                         $value = strip_tags($tmp->display(null, $dfmt));
#TODO         } elseif ($node->linkText === "DEAT:PLAC") {
#TODO             $html = $node->record()->fullName();
#TODO                         $tmp   = new Place($value, $this->tree);
#TODO                         $value = $tmp->shortName();
        } else {
            $html = $node->linkText();
        }

        return new HtmlElement('a', ['href' => $href], $html);
    }
}
