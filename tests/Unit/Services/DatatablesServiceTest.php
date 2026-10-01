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

namespace Fisharebest\Webtrees\Tests\Unit\Services;

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Enums\HttpRequestMethod;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Tests\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

use function json_decode;

use const JSON_THROW_ON_ERROR;

#[CoversClass(DatatablesService::class)]
class DatatablesServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::createDatabase();
    }

    public function testHandleQueryFiltersSortsAndPaginates(): void
    {
        foreach (['Jane Smith', 'John Smith', 'John Jones'] as $name) {
            DB::query(table: 'log')->insert(['log_type' => 'edit', 'log_message' => $name, 'ip_address' => '127.0.0.1']);
        }

        $request = self::createRequest(HttpRequestMethod::POST->value)
            ->withParsedBody([
                'draw'   => '7',
                'start'  => '1',
                'length' => '1',
                'search' => ['value' => 'John'],
                'order'  => [['column' => '0', 'dir' => 'desc']],
            ]);

        $response = (new DatatablesService())->handleQuery(
            $request,
            DB::query()->select('log_message')->from('log')->whereEqual('log_type', 'edit'),
            ['log_message'],
            ['log_message'],
            static fn (object $row): array => [$row->log_message],
            [],
        );

        $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(7, $data['draw']);
        self::assertSame(3, $data['recordsTotal']);
        self::assertSame(2, $data['recordsFiltered']);
        self::assertSame([['John Jones']], $data['data']);
    }

    public function testHandleQuerySortsAscending(): void
    {
        foreach (['Jane Smith', 'John Smith', 'John Jones'] as $name) {
            DB::query(table: 'log')->insert(['log_type' => 'edit', 'log_message' => $name, 'ip_address' => '127.0.0.1']);
        }

        $request = self::createRequest(HttpRequestMethod::POST->value)
            ->withParsedBody(['order' => [['column' => '0', 'dir' => 'asc']]]);

        $response = (new DatatablesService())->handleQuery(
            $request,
            DB::query()->select('log_message')->from('log')->whereEqual('log_type', 'edit'),
            ['log_message'],
            ['log_message'],
            static fn (object $row): array => [$row->log_message],
            [],
        );

        $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame([['Jane Smith'], ['John Jones'], ['John Smith']], $data['data']);
    }

    public function testHandleQuerySortsByMultipleColumns(): void
    {
        foreach (
            [
                ['auth', 'First'],
                ['edit', 'Second'],
                ['auth', 'Third'],
                ['edit', 'Fourth'],
            ] as [$type, $message]
        ) {
            DB::query(table: 'log')->insert(['log_type' => $type, 'log_message' => $message, 'ip_address' => '127.0.0.1']);
        }

        $request = self::createRequest(HttpRequestMethod::POST->value)
            ->withParsedBody(['order' => [
                ['column' => '0', 'dir' => 'asc'],
                ['column' => '1', 'dir' => 'desc'],
            ]]);

        $response = (new DatatablesService())->handleQuery(
            $request,
            DB::query()->select('log_type', 'log_message')->from('log')->whereIn('log_type', ['auth', 'edit']),
            [],
            ['log_type', 'log_message'],
            static fn (object $row): array => [$row->log_type, $row->log_message],
            [],
        );

        $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame([
            ['auth', 'Third'],
            ['auth', 'First'],
            ['edit', 'Second'],
            ['edit', 'Fourth'],
        ], $data['data']);
    }

    public function testHandleQueryReturnsAllRowsWithoutPageLength(): void
    {
        DB::query(table: 'log')->insert(['log_type' => 'edit', 'log_message' => 'example', 'ip_address' => '127.0.0.1']);

        $response = (new DatatablesService())->handleQuery(
            self::createRequest(params: ['search' => ['value' => 'nomatch']]),
            DB::query()->select('log_message')->from('log')->whereEqual('log_type', 'edit'),
            [],
            [],
            static fn (object $row): array => [$row->log_message],
            [DB::ordinal(1)],
        );

        $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(1, $data['recordsTotal']);
        self::assertSame(1, $data['recordsFiltered']);
        self::assertSame([['example']], $data['data']);
    }

    public function testHandleCollectionUsesPostParameters(): void
    {
        $service = new DatatablesService();

        $collection = new Collection([
            ['name' => 'John Doe'],
            ['name' => 'Jane Doe'],
        ]);

        $request = self::createRequest(
            HttpRequestMethod::POST->value,
            ['draw' => '1'],
            [
                'draw'   => '7',
                'search' => ['value' => 'Jane'],
            ]
        );

        $response = $service->handleCollection(
            $request,
            $collection,
            ['name'],
            [0 => 'name'],
            static fn (array $row): array => [$row['name']]
        );

        $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(7, $data['draw']);
        self::assertSame(1, $data['recordsFiltered']);
        self::assertSame([['Jane Doe']], $data['data']);
    }
}
