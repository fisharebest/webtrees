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

namespace Fisharebest\Webtrees\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function explode;

/**
 * Middleware to detect the client's IP address.
 */
class ClientIp extends \Middlewares\ClientIp
{
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // The configuration comes from config.ini.php, via request attributes.
        $trusted_headers = $this->getCommaSeparatedAttribute($request, 'trusted_headers');
        $trusted_proxies = $this->getCommaSeparatedAttribute($request, 'trusted_proxies');

        $this->proxy($trusted_proxies, $trusted_headers);

        return parent::process($request, $handler);
    }

    /**
     * @param ServerRequestInterface $request
     * @param string                 $attribute
     *
     * @return array<string>
     */
    private function getCommaSeparatedAttribute(ServerRequestInterface $request, string $attribute): array
    {
        $value = $request->getAttribute($attribute);

        if ($value === null) {
            return [];
        }

        return explode(',', $value);
    }
}
