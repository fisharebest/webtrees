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

use Fig\Http\Message\RequestMethodInterface;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Tests\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

use function json_decode;

use const JSON_THROW_ON_ERROR;

#[CoversClass(DatatablesService::class)]
class DatatablesServiceTest extends TestCase
{
    public function testHandleCollectionUsesPostParameters(): void
    {
        $service = new DatatablesService();

        $collection = new Collection([
            ['name' => 'John Doe'],
            ['name' => 'Jane Doe'],
        ]);

        $request = self::createRequest(
            RequestMethodInterface::METHOD_POST,
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
