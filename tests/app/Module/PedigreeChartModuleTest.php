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

namespace Fisharebest\Webtrees\Module;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Services\ChartService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(PedigreeChartModule::class)]
class PedigreeChartModuleTest extends TestCase
{
    protected static bool $uses_database = true;

    /**
     * @return array<string,array{style:string}>
     */
    public static function chartStyles(): array
    {
        return [
            'left'  => ['style' => 'left'],
            'right' => ['style' => 'right'],
            'up'    => ['style' => 'up'],
            'down'  => ['style' => 'down'],
        ];
    }

    #[DataProvider('chartStyles')]
    public function testHandleReturnsPage(string $style): void
    {
        $tree = $this->importTree('demo.ged');
        $user = (new UserService())->create('admin', 'Admin', 'admin@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $module  = new PedigreeChartModule(new ChartService());
        $request = self::createRequest()
            ->withAttribute('tree', $tree)
            ->withAttribute('xref', 'X1030')
            ->withAttribute('style', $style)
            ->withAttribute('generations', 4);

        $response = $module->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
