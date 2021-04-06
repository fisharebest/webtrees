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
use Fisharebest\Webtrees\Services\MapProviderService;
use Illuminate\Database\Eloquent\Collection;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function pathinfo;

use const PATHINFO_BASENAME;
use const PATHINFO_EXTENSION;

/**
 * Import a map provider.
 */
class MapProviderImportPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $data_filesystem      = Registry::filesystem()->data();
        $data_filesystem_name = Registry::filesystem()->dataName();

        try {
            $files = $data_filesystem->listContents('map-providers')
                ->filter(static function (StorageAttributes $attributes): bool {
                    $extension = pathinfo($attributes->path(), PATHINFO_EXTENSION);

                    return $extension === 'json';
                })
                ->map(static function (StorageAttributes $attributes): string {
                    return pathinfo($attributes->path(), PATHINFO_BASENAME);
                })
                ->toArray();
        } catch (FilesystemException $ex) {
            $files = [];
        }

        $files = Collection::make($files)->sort();

        return $this->viewResponse('admin/map-provider-import-form', [
            'folder' => $data_filesystem_name . MapProviderService::PROVIDER_FOLDER,
            'title'  => I18N::translate('Import a map provider'),
            'files'  => $files,
        ]);
    }
}
