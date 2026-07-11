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

namespace Fisharebest\Webtrees\Tests\Unit\Report;

use Fisharebest\Webtrees\Timestamp;
use Fisharebest\Webtrees\Report\HtmlRenderer;
use Fisharebest\Webtrees\Report\PageOrientation;
use Fisharebest\Webtrees\Report\PaperSize;
use Fisharebest\Webtrees\Report\Config;
use Fisharebest\Webtrees\Report\Style;

trait ElementTestTrait
{
    private function makeStyle(string $name = 'text', string $style = '', float $size = 12.0): Style
    {
        return new Style($name, $style, $size);
    }

    private function makeHtmlRenderer(): HtmlRenderer
    {
        $renderer = new HtmlRenderer();

        $renderer->addStyle($this->makeStyle('text', '', 12.0));
        $renderer->addStyle($this->makeStyle('name', 'b', 12.0));
        $renderer->addStyle($this->makeStyle('footnote', '', 8.0));
        $renderer->addStyle($this->makeStyle('genby', '', 8.0));

        $renderer->setup(new Config(
            paper_width: 595.28,
            paper_height: 841.89,
            left_margin: 0.0,
            right_margin: 0.0,
            top_margin: 0.0,
            bottom_margin: 0.0,
            header_margin: 0.0,
            footer_margin: 0.0,
            orientation: PageOrientation::Portrait,
            paper_size: PaperSize::A4,
            rtl: false,
            generated_by: '',
            author: 'tests',
            title: 'test',
            description: '',
            align_rtl: 'left',
            entity_rtl: '&lrm;',
            primary_font: 'dejavusans',
            fallback_fonts: [],
            timestamp: new Timestamp(0, 'UTC', 'en-US'),
        ));

        return $renderer;
    }
}
