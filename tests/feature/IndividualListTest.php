<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees;

use Fisharebest\Localization\Locale\LocaleEnUs;
use Fisharebest\Webtrees\Http\Controllers\ListController;
use Fisharebest\Webtrees\Module\IndividualListModule;
use Fisharebest\Webtrees\Services\IndividualListService;
use Fisharebest\Webtrees\Services\LocalizationService;

/**
 * Test the individual lists.
 *
 * @coversNothing
 */
class IndividualListTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @covers \Fisharebest\Webtrees\Http\Controllers\ListController
     * @return void
     */
    public function testIndividualList(): void
    {
        $tree = $this->importTree('demo.ged');
        $user = Auth::user();
        app()->instance(Tree::class, $tree);
        app()->instance(User::class, $user);

        $list_module             = new IndividualListModule();
        $localization_service    = new LocalizationService(new LocaleEnUs());
        $individual_list_service = new IndividualListService($localization_service, $tree);
        $controller              = new ListController($individual_list_service, $localization_service);

        $request  = self::createRequest('GET', [
            'route'  => 'module',
            'module' => 'individual_list',
            'action' => 'List',
        ]);
        $response = $controller->individualList($request, $tree, $user, $list_module);
        $this->assertSame(self::STATUS_OK, $response->getStatusCode());

        $request  = self::createRequest('GET', ['route' => 'module', 'module' => 'individual_list', 'action' => 'List', 'alpha' => 'B']);
        $response = $controller->individualList($request, $tree, $user, $list_module);
        $this->assertSame(self::STATUS_OK, $response->getStatusCode());

        $request  = self::createRequest('GET', ['route' => 'module', 'module' => 'individual_list', 'action' => 'List', 'alpha' => ',']);
        $response = $controller->individualList($request, $tree, $user, $list_module);
        $this->assertSame(self::STATUS_OK, $response->getStatusCode());

        $request  = self::createRequest('GET', ['route' => 'module', 'module' => 'individual_list', 'action' => 'List', 'alpha' => '@']);
        $response = $controller->individualList($request, $tree, $user, $list_module);
        $this->assertSame(self::STATUS_OK, $response->getStatusCode());

        $request  = self::createRequest('GET', ['route' => 'module', 'module' => 'individual_list', 'action' => 'List', 'surname' => 'BRAUN']);
        $response = $controller->individualList($request, $tree, $user, $list_module);
        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }
}
