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
use Fisharebest\Webtrees\Report\DocumentBuilder;
use Fisharebest\Webtrees\Report\Section;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DocumentBuilder::class)]
class DocumentBuilderTest extends TestCase
{
    public function testBuildsDocumentBySection(): void
    {
        $report_document_builder = new DocumentBuilder();

        $report_document_builder->setProcessing(Section::Header);
        $report_document_builder->addElement(new Element());

        $report_document_builder->setProcessing(Section::Body);
        $report_document_builder->addElement(new Element());

        $report_document_builder->setProcessing(Section::Footer);
        $report_document_builder->addElement(new Element());

        $report_document = $report_document_builder->reportDocument('Title');

        self::assertSame('Title', $report_document->title);
        self::assertCount(1, $report_document->header_elements);
        self::assertCount(1, $report_document->body_elements);
        self::assertCount(1, $report_document->footer_elements);
    }
}
