<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\TreeService;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function explode;

/**
 * Show old files that could be deleted.
 */
class CleanDataFolder implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private TreeService $tree_service;

    /**
     * CleanDataFolder constructor.
     *
     * @param TreeService $tree_service
     */
    public function __construct(TreeService $tree_service)
    {
        $this->tree_service = $tree_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data_filesystem = Registry::filesystem()->data();

        $this->layout = 'layouts/administration';

        $protected = [
            '.htaccess',
            '.gitignore',
            'index.php',
            'config.ini.php',
        ];

        if ($request->getAttribute('dbtype') === 'sqlite') {
            $protected[] = $request->getAttribute('dbname') . '.sqlite';
        }

        // Protect the media folders
        foreach ($this->tree_service->all() as $tree) {
            $media_directory = $tree->getPreference('MEDIA_DIRECTORY');
            [$folder] = explode('/', $media_directory);

            $protected[] = $folder . '/';
        }

        // List the top-level contents of the data folder
        try {
            $entries = $data_filesystem->listContents('', Filesystem::LIST_SHALLOW)
                ->map(static function (StorageAttributes $attributes): string {
                    if ($attributes->isDir()) {
                        return $attributes->path() . '/';
                    }

                    return $attributes->path();
                })
                ->toArray();
        } catch (FilesystemException $ex) {
            $entries = [];
        }


        return $this->viewResponse('admin/clean-data', [
            'title'     => I18N::translate('Clean up data folder'),
            'entries'   => $entries,
            'protected' => $protected,
        ]);
    }
}
