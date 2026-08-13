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

use Fisharebest\Webtrees\Report\Element;
use Fisharebest\Webtrees\Report\Document;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Document::class)]
class DocumentTest extends TestCase
{
    public function testConstructorStoresSectionElements(): void
    {
        $header_element = new Element();
        $body_element = new Element();
        $footer_element = new Element();

        $report_document = new Document(
            'Title',
            [$header_element],
            [$body_element],
            [$footer_element],
        );

        self::assertSame('Title', $report_document->title);
        self::assertCount(1, $report_document->header_elements);
        self::assertCount(1, $report_document->body_elements);
        self::assertCount(1, $report_document->footer_elements);
    }

    public function testEmptyFactoryCreatesDocumentWithNoElements(): void
    {
        $report_document = Document::empty('Empty');

        self::assertSame('Empty', $report_document->title);
        self::assertSame([], $report_document->header_elements);
        self::assertSame([], $report_document->body_elements);
        self::assertSame([], $report_document->footer_elements);
    }
}
