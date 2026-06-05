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

abstract class AbstractRenderer implements RendererInterface, DocumentAcceptorInterface, StyleConsumerInterface, ElementRendererInterface
{
    protected const string UNITS = 'pt';

    public Config $config;

    private readonly RenderContext $render_context;

    public function __construct()
    {
        $this->render_context = new RenderContext();
    }

    public function applyDocument(Document $report_document): void
    {
        $this->render_context->applyDocument($report_document);
    }

    abstract public function output(): string;

    abstract public function setCurrentStyle(Style $style): void;

    abstract public function getStringWidth(string $text): float;

    public function setup(Config $config): void
    {
        $this->config = $config;
    }

    public function reportConfig(): Config
    {
        if (!isset($this->config)) {
            throw new LogicException('Report configuration is not initialized. Is there a <Doc> element?');
        }

        return $this->config;
    }

    public function addStyle(Style $style): void
    {
        $this->render_context->addStyle($style);
    }

    public function getStyle(string $style): Style
    {
        return $this->render_context->getStyle($style);
    }

    /** @return list<Element> */
    protected function headerElements(): array
    {
        return $this->render_context->headerElements();
    }

    /** @return list<Element> */
    protected function bodyElements(): array
    {
        return $this->render_context->bodyElements();
    }

    /** @return list<Element> */
    protected function footerElements(): array
    {
        return $this->render_context->footerElements();
    }

    /** @return array<string, Style> */
    protected function stylesMap(): array
    {
        return $this->render_context->stylesMap();
    }

    protected function currentStyleValue(): Style|null
    {
        return $this->render_context->currentStyle();
    }

    protected function setCurrentStyleValue(Style|null $style): void
    {
        $this->render_context->setCurrentStyle($style);
    }

    abstract public function newPage(): void;

    abstract public function pageNumber(): int;
}
