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
 * Mutable builder used while parsing report XML into a Document.
 */
final class DocumentBuilder
{
    private Section $processing = Section::Header;

    /** @var list<Element> */
    private array $header_elements = [];

    /** @var list<Element> */
    private array $body_elements = [];

    /** @var list<Element> */
    private array $footer_elements = [];

    public function setProcessing(Section $section): void
    {
        $this->processing = $section;
    }

    public function addElement(Element $element): void
    {
        match ($this->processing) {
            Section::Body => $this->body_elements[] = $element,
            Section::Header => $this->header_elements[] = $element,
            Section::Footer => $this->footer_elements[] = $element,
        };
    }

    public function reportDocument(string $title): Document
    {
        return new Document(
            $title,
            $this->header_elements,
            $this->body_elements,
            $this->footer_elements,
        );
    }
}
