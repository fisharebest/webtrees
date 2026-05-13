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

namespace Fisharebest\Webtrees\Http\Middleware;

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Http\RequestHandlers\SetupWizard;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

/**
 * When a deployment ships a pre-written config.ini.php (e.g. a container image
 * that injects DB credentials from environment variables) but has not yet
 * created an administrator account, the normal request pipeline can only
 * respond with 404: every route assumes at least one user exists. This
 * middleware diverts such requests to the setup wizard's administrator step,
 * carrying the already-known database settings forward so the operator only
 * has to fill in the admin account.
 *
 * Requires the schema migration to have run (the user table must exist); the
 * surrounding pipeline guarantees this — UpdateDatabaseSchema runs before us.
 */
class BootstrapAdminWizard implements MiddlewareInterface
{
    public function __construct(
        private readonly SetupWizard $setup_wizard,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Only intervene when ReadConfigIni populated the request from
        // config.ini.php — otherwise we are in a fresh install where the
        // setup wizard is already the active handler.
        if ($request->getAttribute('dbhost') === null) {
            return $handler->handle($request);
        }

        if ($this->administratorExists()) {
            return $handler->handle($request);
        }

        // Jump straight to the admin step. Pre-existing config.ini.php values
        // travel as request attributes; SetupWizard::userData() picks them up
        // as defaults so the hidden DB fields in step-5 are correctly seeded.
        $body          = $request->getParsedBody();
        $body          = is_array($body) ? $body : [];
        $body['step']  = 5;

        return $this->setup_wizard->handle($request->withParsedBody($body));
    }

    /**
     * Returns true when at least one administrator account exists. Any
     * database error (missing tables, lost connection) is treated as "yes" so
     * the regular pipeline can surface the real failure instead of silently
     * routing to the wizard.
     */
    private function administratorExists(): bool
    {
        try {
            return DB::table('user_setting')
                ->where('setting_name', '=', UserInterface::PREF_IS_ADMINISTRATOR)
                ->where('setting_value', '=', '1')
                ->exists();
        } catch (Throwable) {
            return true;
        }
    }
}
