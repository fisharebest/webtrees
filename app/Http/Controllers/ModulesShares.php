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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleShareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function redirect;
use function route;
use function view;

final class ModulesShares extends AbstractModuleComponent
{
    public function get(ServerRequestInterface $request): ResponseInterface
    {
        return $this->listComponents(
            ModuleShareInterface::class,
            view('icons/sidebar') . ' ' . I18N::translate('Shares'),
            ''
        );
    }

    public function post(ServerRequestInterface $request): ResponseInterface
    {
        $this->updateStatus(ModuleShareInterface::class, $request);

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');

        return redirect(route(self::class));
    }
}
