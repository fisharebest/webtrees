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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\MapDataService;
use Illuminate\Database\Eloquent\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function strtolower;

/**
 * Import geographic data.
 */
class MapDataImportPage implements RequestHandlerInterface
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

        $files = Collection::make($data_filesystem->listContents('places'))
            ->filter(static function (array $metadata): bool {
                $extension = strtolower($metadata['extension'] ?? '');

                return $extension === 'csv' || $extension === 'geojson';
            })
            ->map(static function (array $metadata): string {
                return $metadata['basename'];
            })
            ->sort();

        return $this->viewResponse('admin/map-import-form', [
            'folder' => $data_filesystem_name . MapDataService::PLACES_FOLDER,
            'title'  => I18N::translate('Import geographic data'),
            'files'  => $files,
        ]);
    }
}
