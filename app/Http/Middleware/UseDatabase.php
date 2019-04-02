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

namespace Fisharebest\Webtrees\Http\Middleware;

use function file_exists;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Http\Controllers\SetupController;
use Fisharebest\Webtrees\Webtrees;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function parse_ini_file;

/**
 * Middleware to connect to the database.
 */
class UseDatabase implements MiddlewareInterface
{
    /** @var SetupController $controller */
    private $setup_controller;

    /**
     * @param SetupController $setup_controller
     */
    public function __construct(SetupController $setup_controller)
    {
        $this->setup_controller = $setup_controller;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Read the connection settings and create the database
        if (file_exists(Webtrees::CONFIG_FILE)) {
            $database_config = parse_ini_file(Webtrees::CONFIG_FILE);

            Database::connect($database_config);

            return $handler->handle($request);
        }

        // No database connection? Run the setup wizard to create one.
        return $this->setup_controller->setup($request);
    }
}
