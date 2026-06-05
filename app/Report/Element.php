<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Report;

use LogicException;

use function preg_replace;
use function str_contains;
use function strip_tags;
use function trim;

/**
 * Base class for every report element.
 */
class Element
{
    // Special value for X or Y position, to indicate the current position.
    public const float CURRENT_POSITION = -1.0;

    /**
     * Placeholder substring written into element text when the parser
     * encounters <PageNum/>. Resolved during layout/rendering.
     */
    private const string PAGE_NUMBER_TOKEN = '#PAGENUM#';

    /**
     * Placeholder substring written into element text when the parser
     * encounters <TotalPages/>.  This token is replaced with the actual page
     * count in the PDF content streams before serialization.  The HTML backend
     * produces an unpaginated document and skips any element that contains
     * this placeholder via containsTotalPages().
     *
     * The token uses only characters that are not BiDi-mirrored, so that RTL
     * text reordering in the PDF library does not mangle the placeholder.
     */
    public const string TOTAL_PAGES_TOKEN = '#:PTP:#';

    protected string $text = '';


    public function addText(string $text): void
    {
        // Whitespace-only chunks that contain a newline are XML formatting
        // (indentation between tags on different lines) — not meaningful content.
        // Intentional spaces are always same-line content without newlines.
        if (str_contains($text, "\n") && trim($text) === '') {
            return;
        }

        $text = \strtr($text, ["\t" => ' ', "\n" => ' ']);

        // The handler for <br/> inserts a '<br>' string into the text.
        // There should be no HTML entities. The XMLReader will error if they
        // exist, or convert them if defined using: <!ENTITY nbsp "&#xA0;">

        $text = strtr($text, ['<br>' => "\n"]);

        // Embedded variables report logic should not generate HTML.
        if ($text !== strip_tags($text)) {
            throw new LogicException('HTML tags are not allowed in text: ' . $text);
        }

        $this->text = preg_replace('/ {2,}/', ' ', $this->text . $text);
    }

    /**
     * Append a placeholder that will be substituted with the current
     * page number when the element is rendered.
     */
    public function addPageNumber(): void
    {
        $this->text .= self::PAGE_NUMBER_TOKEN;
    }

    /**
     * Append a placeholder that will be substituted with the total page
     * count.  PDF substitution is performed by TCPDF at PDF assembly
     * time; HTML output skips any element that contains the placeholder
     * since the HTML backend produces an unpaginated document.
     */
    public function addTotalPages(): void
    {
        $this->text .= self::TOTAL_PAGES_TOKEN;
    }

    /**
     * True when the element's text stream contains a <TotalPages/>
     * placeholder.  Used by the HTML backend to suppress cells whose
     * only purpose is to print "Page X of Y" footers.
     */
    public function containsTotalPages(): bool
    {
        return str_contains($this->text, self::TOTAL_PAGES_TOKEN);
    }


    public function getValue(): string
    {
        return $this->text;
    }
}
