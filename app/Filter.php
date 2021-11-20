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

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\CommonMark\CensusTableExtension;
use Fisharebest\Webtrees\CommonMark\ResponsiveTableExtension;
use Fisharebest\Webtrees\CommonMark\XrefExtension;
use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\Block\Renderer\DocumentRenderer;
use League\CommonMark\Block\Renderer\ParagraphRenderer;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Inline\Parser\AutolinkParser;
use League\CommonMark\Inline\Renderer\LinkRenderer;
use League\CommonMark\Inline\Renderer\TextRenderer;

/**
 * Filter input and escape output.
 *
 * @deprecated since 2.0.17. Will be removed in 2.1.0.
 */
class Filter
{
    /**
     * Format block-level text such as notes or transcripts, etc.
     *
     * @param string $text
     * @param Tree   $tree
     *
     * @return string
     */
    public static function formatText(string $text, Tree $tree): string
    {
        switch ($tree->getPreference('FORMAT_TEXT')) {
            case 'markdown':
                return self::markdown($text, $tree);
            default:
                return self::expandUrls($text, $tree);
        }
    }

    /**
     * Format a block of text, expanding URLs and XREFs.
     *
     * @param string $text
     * @param Tree   $tree
     *
     * @return string
     */
    public static function expandUrls(string $text, Tree $tree): string
    {
        return Registry::markdownFactory()->autolink($tree)->convertToHtml($text);
    }

    /**
     * Format a block of text, using "Markdown".
     *
     * @param string $text
     * @param Tree   $tree
     *
     * @return string
     */
    public static function markdown(string $text, Tree $tree): string
    {
        return Registry::markdownFactory()->markdown($tree)->convertToHtml($text);
    }
}
