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
 * Shared mutable rendering state previously stored directly on AbstractRenderer.
 */
final class RenderContext
{
    /** @var array<string, Style> */
    private array $styles = [];

    /** @var list<Element> */
    private array $header_elements = [];

    /** @var list<Element> */
    private array $footer_elements = [];

    /** @var list<Element> */
    private array $body_elements = [];

    private Style|null $current_style = null;


    public function applyDocument(Document $report_document): void
    {
        $this->header_elements = $report_document->header_elements;
        $this->body_elements   = $report_document->body_elements;
        $this->footer_elements = $report_document->footer_elements;
    }


    public function addStyle(Style $style): void
    {
        $this->styles[$style->name] = $style;
    }

    public function getStyle(string $style): Style
    {
        return $this->styles[$style];
    }

    /** @return array<string, Style> */
    public function stylesMap(): array
    {
        return $this->styles;
    }

    /** @return list<Element> */
    public function headerElements(): array
    {
        return $this->header_elements;
    }

    /** @return list<Element> */
    public function bodyElements(): array
    {
        return $this->body_elements;
    }

    /** @return list<Element> */
    public function footerElements(): array
    {
        return $this->footer_elements;
    }

    public function currentStyle(): Style|null
    {
        return $this->current_style;
    }

    public function setCurrentStyle(Style|null $style): void
    {
        $this->current_style = $style;
    }
}
