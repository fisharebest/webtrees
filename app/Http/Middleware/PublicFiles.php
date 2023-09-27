<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Mime;
use Fisharebest\Webtrees\Webtrees;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function file_exists;
use function file_get_contents;
use function pathinfo;
use function response;
use function str_starts_with;
use function strtoupper;

use const PATHINFO_EXTENSION;

/**
 * Provide access to files in the folder /public, for cli-server requests and in case the web-server doesn't do this.
 */
class PublicFiles implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        if (str_starts_with($path, '/public/') && !str_contains($path, '..')) {
            $file = Webtrees::ROOT_DIR . $path;

            if (file_exists($file)) {
                $content   = file_get_contents($file);
                $extension = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
                $mime_type = Mime::TYPES[$extension] ?? Mime::DEFAULT_TYPE;

                return response($content, StatusCodeInterface::STATUS_OK, [
                    'cache-control' => 'public,max-age=31536000',
                    'content-type'  => $mime_type,
                ]);
            }
        }

        return $handler->handle($request);
    }
}
