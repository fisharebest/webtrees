<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\PhpService;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UpgradeService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UpgradeWizardPage::class)]
class UpgradeWizardPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testWizard(): void
    {
        $timeout_service       = new TimeoutService(php_service: new PhpService());
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $upgrade_service       = new UpgradeService($timeout_service);
        $handler               = new UpgradeWizardPage($tree_service, $upgrade_service);
        $request               = self::createRequest();
        $response              = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
