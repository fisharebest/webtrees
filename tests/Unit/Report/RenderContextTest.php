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

use Fisharebest\Webtrees\Report\Document;
use Fisharebest\Webtrees\Report\RenderContext;
use Fisharebest\Webtrees\Report\Style;
use Fisharebest\Webtrees\Report\Text;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RenderContext::class)]
class RenderContextTest extends TestCase
{
    public function testAddAndGetStyle(): void
    {
        $context = new RenderContext();
        $style   = new Style('heading', 'b', 14.0);

        $context->addStyle($style);

        self::assertSame($style, $context->getStyle('heading'));
    }

    public function testStylesMap(): void
    {
        $context = new RenderContext();
        $style1  = new Style('body', '', 12.0);
        $style2  = new Style('title', 'b', 16.0);

        $context->addStyle($style1);
        $context->addStyle($style2);

        $map = $context->stylesMap();

        self::assertCount(2, $map);
        self::assertArrayHasKey('body', $map);
        self::assertArrayHasKey('title', $map);
    }

    public function testCurrentStyleDefaultsToNull(): void
    {
        $context = new RenderContext();

        self::assertNull($context->currentStyle());
    }

    public function testSetCurrentStyle(): void
    {
        $context = new RenderContext();
        $style   = new Style('body', '', 12.0);

        $context->setCurrentStyle($style);

        self::assertSame($style, $context->currentStyle());
    }

    public function testSetCurrentStyleToNull(): void
    {
        $context = new RenderContext();
        $style   = new Style('body', '', 12.0);

        $context->setCurrentStyle($style);
        $context->setCurrentStyle(null);

        self::assertNull($context->currentStyle());
    }

    public function testApplyDocumentSetsElements(): void
    {
        $context  = new RenderContext();
        $style    = new Style('body', '', 12.0);
        $header   = new Text($style, '', 0.0);
        $body     = new Text($style, '', 0.0);
        $footer   = new Text($style, '', 0.0);
        $document = new Document(
            title: 'Test Report',
            header_elements: [$header],
            body_elements: [$body],
            footer_elements: [$footer],
        );

        $context->applyDocument($document);

        self::assertSame([$header], $context->headerElements());
        self::assertSame([$body], $context->bodyElements());
        self::assertSame([$footer], $context->footerElements());
    }

    public function testElementsDefaultToEmpty(): void
    {
        $context = new RenderContext();

        self::assertSame([], $context->headerElements());
        self::assertSame([], $context->bodyElements());
        self::assertSame([], $context->footerElements());
    }
}
