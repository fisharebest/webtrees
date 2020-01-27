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

use Fisharebest\Webtrees\Cache;
use Fisharebest\Webtrees\Webtrees;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

use function app;

/**
 * Middleware to setup an in-memory and file-system cache.
 */
class UseCache implements MiddlewareInterface
{
    // How frequently to perform garbage collection.
    private const GC_PROBABILITY = 1000;

    // Filesystem cache parameters.
    private const FILES_TTL = 8640000;
    private const FILES_DIR = Webtrees::DATA_DIR . 'cache/';

    /** @var ArrayAdapter */
    private $array_adapter;

    /** @var FilesystemAdapter */
    private $files_adapter;

    /**
     * UseCache constructor.
     */
    public function __construct()
    {
        $this->array_adapter = new ArrayAdapter(0, false);
        $this->files_adapter = new FilesystemAdapter('', self::FILES_TTL, self::FILES_DIR);
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Use an array cache for database calls, etc.
        app()->instance('cache.array', new Cache($this->array_adapter));

        // Use a filesystem cache for image thumbnails, etc.
        app()->instance('cache.files', new Cache($this->files_adapter));

        return $handler->handle($request);
    }

    /**
     * Perform garbage collection.
     */
    public function __destruct()
    {
        if (random_int(1, self::GC_PROBABILITY) === 1) {
            $this->files_adapter->prune();
        }
    }
}
