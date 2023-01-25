<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

use Fisharebest\Webtrees\Http\RequestHandlers\SetupWizard;
use Fisharebest\Webtrees\Webtrees;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function file_exists;
use function parse_ini_file;

/**
 * Middleware to read (or create) the webtrees configuration file.
 */
class ReadConfigIni implements MiddlewareInterface
{
    private SetupWizard $setup_wizard;

    /**
     * @param SetupWizard $setup_wizard
     */
    public function __construct(SetupWizard $setup_wizard)
    {
        $this->setup_wizard = $setup_wizard;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Read the configuration settings.
        if (file_exists(Webtrees::CONFIG_FILE)) {
            $config = parse_ini_file(Webtrees::CONFIG_FILE);

            // Store the configuration settings as request attributes.
            foreach ($config as $key => $value) {
                $request = $request->withAttribute($key, $value);
            }
        } else {
            // No configuration file? Run the setup wizard to create one.
            $handler = $this->setup_wizard;
        }

        return $handler->handle($request);
    }
}
