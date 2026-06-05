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

/**
 * Writes layout blocks to the PDF backend.
 */
final class PdfWriter
{
    /**
     * @param list<Element> $elements
     */
    public function renderBody(PdfRenderTargetInterface $renderer, array $elements): void
    {
        // Keep one initial page so page-indexed block rendering starts from a valid page.
        $renderer->newPage();

        $config = $renderer->reportConfig();
        $engine = new LayoutEngine(new PdfTextMeasurer($renderer), $config, false);
        $pages = $engine->layoutPaged($elements);

        $pdf_block_writer = new PdfBlockWriter();
        $pdf_block_writer->render(
            $renderer,
            $pages->flatten(),
            $config->left_margin,
            $config->top_margin,
        );
    }

    /** @param list<Element> $elements */
    public function renderFixedSection(PdfRenderTargetInterface $renderer, array $elements, float $origin_x, float $origin_y): void
    {
        $engine = new LayoutEngine(new PdfTextMeasurer($renderer), $renderer->reportConfig(), false);
        $blocks = $engine->layout($elements, $renderer->getPageIndex());

        $pdf_block_writer = new PdfBlockWriter();
        $pdf_block_writer->renderCurrentPage($renderer, $blocks, $origin_x, $origin_y);
    }
}
