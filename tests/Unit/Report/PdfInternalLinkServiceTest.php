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

use Fisharebest\Webtrees\Report\PdfInternalLinkService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PdfInternalLinkService::class)]
class PdfInternalLinkServiceTest extends TestCase
{
    public function testResolveDestinationReturnsOriginalUrlWhenLinkIsUnknown(): void
    {
        $pdf_internal_link_service = new PdfInternalLinkService();

        $result = $pdf_internal_link_service->resolveDestination(
            url: 'https://example.com',
            create_internal_destination: static fn (int $page, float $y): string => $page . ':' . $y,
        );

        self::assertSame('https://example.com', $result);
    }

    public function testResolveDestinationUsesStoredInternalLinkDestination(): void
    {
        $pdf_internal_link_service = new PdfInternalLinkService();

        $link_id = $pdf_internal_link_service->createLink(2);
        $pdf_internal_link_service->setDestination($link_id, 42.5, 2, 5);

        $result = $pdf_internal_link_service->resolveDestination(
            url: (string) $link_id,
            create_internal_destination: static fn (int $page, float $y): string => $page . ':' . $y,
        );

        self::assertSame('5:42.5', $result);
    }

    public function testSetDestinationUsesCurrentPageWhenPageIsNotProvided(): void
    {
        $pdf_internal_link_service = new PdfInternalLinkService();

        $link_id = $pdf_internal_link_service->createLink(1);
        $pdf_internal_link_service->setDestination($link_id, 10.0, 7);

        $result = $pdf_internal_link_service->resolveDestination(
            url: (string) $link_id,
            create_internal_destination: static fn (int $page, float $y): string => $page . ':' . $y,
        );

        self::assertSame('7:10', $result);
    }
}
