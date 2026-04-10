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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Contracts\GedcomRecordFactoryInterface;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MergeRecordsPage::class)]
class MergeRecordsPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(MergeRecordsPage::class));
    }

    public function testHandleReturnsOkWithNoRecords(): void
    {
        $tree = $this->importTree('demo.ged');

        $record_factory = $this->createMock(GedcomRecordFactoryInterface::class);
        $record_factory
            ->expects($this->exactly(2))
            ->method('make')
            ->willReturn(null);

        Registry::gedcomRecordFactory($record_factory);

        $handler  = new MergeRecordsPage();
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            ['xref1' => '', 'xref2' => ''],
            [],
            [],
            ['tree' => $tree],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleReturnsOkWithIndividualRecords(): void
    {
        $tree = $this->importTree('demo.ged');

        $individual1 = self::createStub(Individual::class);
        $individual1->method('xref')->willReturn('I1');
        $individual1->method('tree')->willReturn($tree);
        $individual1->method('canShow')->willReturn(true);

        $individual2 = self::createStub(Individual::class);
        $individual2->method('xref')->willReturn('I2');
        $individual2->method('tree')->willReturn($tree);
        $individual2->method('canShow')->willReturn(true);

        $record_factory = $this->createMock(GedcomRecordFactoryInterface::class);
        $record_factory
            ->expects($this->exactly(2))
            ->method('make')
            ->willReturnCallback(static fn (string $xref) => match ($xref) {
                'I1'    => $individual1,
                'I2'    => $individual2,
                default => null,
            });

        Registry::gedcomRecordFactory($record_factory);

        $handler  = new MergeRecordsPage();
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            ['xref1' => 'I1', 'xref2' => 'I2'],
            [],
            [],
            ['tree' => $tree],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
