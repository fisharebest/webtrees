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

use Fisharebest\Webtrees\Tree;
use League\CommonMark\Extension\Extension;
use League\CommonMark\Inline\Parser\InlineParserInterface;

/**
 * Convert XREFs within markdown text to links
 */
class XrefExtension extends Extension
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
     * @return InlineParserInterface[]
     */
    public function getInlineParsers(): array
    {
        return [
            new XrefParser($this->tree),
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'xref';
    }
}
