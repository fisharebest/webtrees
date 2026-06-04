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

use function str_contains;
use function str_replace;
use function strip_tags;
use function trim;

/**
 * Base class for every report element.
 *
 * The TRenderer template parameter records which renderer family the
 * element is designed for, so the element's render(), getHeight(),
 * getWidth() and friends can use renderer methods that only exist on
 * one backend (HtmlRendererInterface or PdfRendererInterface) without
 * either lying about the runtime type or duplicating per-method
 * intersection PHPDoc.  The native parameter type stays AbstractRenderer
 * because PHP forbids narrowing a parameter type in a subclass.
 *
 * @template TRenderer of AbstractRenderer
 */
abstract class AbstractElement
{
    // Special value for X or Y position, to indicate the current position.
    public const float CURRENT_POSITION = -1.0;

    /**
     * Placeholder substring written into element text when the parser
     * encounters <PageNum/>.  Resolved per-renderer in resolvedText().
     */
    private const string PAGE_NUMBER_TOKEN = '#PAGENUM#';

    /**
     * Placeholder substring written into element text when the parser
     * encounters <TotalPages/>.  Identical to TCPDF's $alias_tot_pages so
     * the PDF backend can rely on TCPDF's automatic substitution at PDF
     * assembly time.  The HTML backend produces an unpaginated document
     * and skips any element that contains this placeholder via
     * containsTotalPages().
     */
    private const string TOTAL_PAGES_TOKEN = '{{:ptp:}}';

    protected string $text = '';

    /**
     * @param TRenderer $renderer
     * @param bool      $layout - true (element handles layout) false (parent handles layout)
     */
    abstract public function render(AbstractRenderer $renderer, bool $layout = true): void;

    /**
     * @param TRenderer $renderer
     */
    public function getHeight(AbstractRenderer $renderer): float
    {
        return 0.0;
    }

    /**
     * @param TRenderer $renderer
     *
     * @return array{0:float,1:int,2:float}
     */
    public function getWidth(AbstractRenderer $renderer): array
    {
        return [0.0, 1, 0.0];
    }

    public function addText(string $t): void
    {
        // The handler for <br/> inserts a '<br>' string into the text.
        // There should be no HTML entities. The XMLReader will error if they
        // exist, or convert them if defined using: <!ENTITY nbsp "&#xA0;">

        $t = trim($t, "\r\n\t");
        $t = strtr($t, ['<br>' => "\n"]);

        // Embedded variables report logic should not generate HTML.
        if ($t !== strip_tags($t)) {
            throw new LogicException('HTML tags are not allowed in text: ' . $t);
        }

        $this->text .= strip_tags($t);
    }

    public function addNewline(): void
    {
        $this->text .= "\n";
    }

    /**
     * Append a placeholder that will be substituted with the current
     * page number when the element is rendered.  See resolvedText().
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

    /**
     * Return the element text with dynamic placeholders substituted by
     * values queried from the active renderer.  Element render methods
     * call this just before consuming the text for measurement, wrapping
     * or output, instead of touching $this->text directly.
     *
     * The total-pages placeholder is intentionally left in place: TCPDF
     * substitutes it during PDF assembly, and the HTML backend filters
     * affected cells out earlier via containsTotalPages().
     *
     * @param TRenderer $renderer
     */
    public function resolvedText(AbstractRenderer $renderer): string
    {
        return str_replace(
            [self::PAGE_NUMBER_TOKEN, '«', '»'],
            [(string) $renderer->pageNo(), '<u>', '</u>'],
            $this->text
        );
    }

    public function getValue(): string
    {
        return $this->text;
    }

    public function setWrapWidth(float $wrapwidth, float $cellwidth): float
    {
        return 0;
    }

    /**
     * @param TRenderer $renderer
     */
    public function renderFootnote(AbstractRenderer $renderer): void
    {
    }
}
