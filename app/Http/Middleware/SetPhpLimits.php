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

use Closure;
use Exception;
use Fisharebest\Webtrees\Site;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to modify the PHP configuration.
 */
class SetPhpLimits implements MiddlewareInterface
{
    /**
     * Request more resources - if we can/want to
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return Response
     * @throws Exception
     */
    public function handle(Request $request, Closure $next): Response
    {
        $memory_limit = Site::getPreference('MEMORY_LIMIT');

        if ($memory_limit !== '' && strpos(ini_get('disable_functions'), 'ini_set') === false) {
            ini_set('memory_limit', $memory_limit);
        }

        $max_execution_time = Site::getPreference('MAX_EXECUTION_TIME');

        if ($max_execution_time !== '' && strpos(ini_get('disable_functions'), 'set_time_limit') === false) {
            set_time_limit((int) $max_execution_time);
        }

        return $next($request);
    }
}
