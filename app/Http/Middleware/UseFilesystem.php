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

use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Webtrees;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\Storage\Memory;
use League\Flysystem\Filesystem;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware to set the data storage area.
 */
class UseFilesystem implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $data_dir = Site::getPreference('INDEX_DIRECTORY', Webtrees::DATA_DIR);
        $root_dir = __DIR__ . '/../../..';

        $data_filesystem = new Filesystem(new CachedAdapter(new Local($data_dir), new Memory()));
        $root_filesystem = new Filesystem(new CachedAdapter(new Local($root_dir), new Memory()));

        $request = $request
            ->withAttribute('filesystem.data', $data_filesystem)
            ->withAttribute('filesystem.data.name', $data_dir)
            ->withAttribute('filesystem.root', $root_filesystem)
            ->withAttribute('filesystem.root.name', realpath($root_dir) . '/');

        return $handler->handle($request);
    }
}
