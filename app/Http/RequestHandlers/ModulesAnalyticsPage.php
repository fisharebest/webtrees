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

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleAnalyticsInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function view;

final class ModulesAnalyticsPage extends AbstractModuleComponentPage
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->listComponents(
            ModuleAnalyticsInterface::class,
            view('icons/analytics') . ' ' . I18N::translate('Tracking and analytics'),
            I18N::translate('If you use one of the following tracking and analytics services, webtrees can add the tracking codes automatically.')
        );
    }
}
