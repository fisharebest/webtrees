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

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;
use function route;

final class TelemetrySettingsAction implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $endpointUrl    = Validator::parsedBody($request)->string('TELEMETRY_ENDPOINT_URL');
        $publishableKey = Validator::parsedBody($request)->string('TELEMETRY_PUBLISHABLE_KEY');

        Site::setPreference('TELEMETRY_ENDPOINT_URL', $endpointUrl);
        Site::setPreference('TELEMETRY_PUBLISHABLE_KEY', $publishableKey);

        FlashMessages::addMessage(I18N::translate('The telemetry settings have been updated.'), 'success');

        return redirect(route(ControlPanel::class));
    }
}
