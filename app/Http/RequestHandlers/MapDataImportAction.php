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

use Exception;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\PlaceLocation;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\MapDataService;
use Illuminate\Database\Capsule\Manager as DB;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCheckFileExistence;
use League\Flysystem\UnableToReadFile;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_filter;
use function array_reverse;
use function array_slice;
use function count;
use function fclose;
use function fgetcsv;
use function implode;
use function is_numeric;
use function json_decode;
use function redirect;
use function rewind;
use function route;
use function str_contains;
use function stream_get_contents;

use const JSON_THROW_ON_ERROR;
use const UPLOAD_ERR_OK;

/**
 * Import geographic data.
 */
class MapDataImportAction implements RequestHandlerInterface
{
    private MapDataService $map_data_service;

    /**
     * MapDataImportAction constructor.
     *
     * @param MapDataService $map_data_service
     */
    public function __construct(MapDataService $map_data_service)
    {
        $this->map_data_service = $map_data_service;
    }

    /**
     * This function assumes the input file layout is
     * level followed by a variable number of placename fields
     * followed by Longitude, Latitude, Zoom & Icon
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data_filesystem = Registry::filesystem()->data();

        $params = (array) $request->getParsedBody();

        $serverfile     = $params['serverfile'] ?? '';
        $options        = $params['import-options'] ?? '';
        $local_file     = $request->getUploadedFiles()['localfile'] ?? null;

        $places = [];

        $url = route(MapDataList::class, ['parent_id' => 0]);

        $fp = false;

        try {
            $file_exists = $data_filesystem->fileExists(MapDataService::PLACES_FOLDER . $serverfile);
        } catch (FilesystemException | UnableToCheckFileExistence $ex) {
            $file_exists = false;
        }


        if ($serverfile !== '' && $file_exists) {
            // first choice is file on server
            try {
                $fp = $data_filesystem->readStream(MapDataService::PLACES_FOLDER . $serverfile);
            } catch (FilesystemException | UnableToReadFile $ex) {
                $fp = false;
            }
        } elseif ($local_file instanceof UploadedFileInterface && $local_file->getError() === UPLOAD_ERR_OK) {
            // 2nd choice is local file
            $fp = $local_file->getStream()->detach();
        }

        if ($fp === false || $fp === null) {
            return redirect($url);
        }

        $string = stream_get_contents($fp);

        // Check the file type
        if (str_contains($string, 'FeatureCollection')) {
            $input_array = json_decode($string, false, 512, JSON_THROW_ON_ERROR);

            foreach ($input_array->features as $feature) {
                $places[] = [
                    'latitude'  => $feature->geometry->coordinates[1],
                    'longitude' => $feature->geometry->coordinates[0],
                    'name'      => $feature->properties->name,
                ];
            }
        } else {
            rewind($fp);
            while (($row = fgetcsv($fp, 0, MapDataService::CSV_SEPARATOR)) !== false) {
                // Skip the header
                if (!is_numeric($row[0])) {
                    continue;
                }

                $level = (int) $row[0];
                $count = count($row);
                $name  = implode(Gedcom::PLACE_SEPARATOR, array_reverse(array_slice($row, 1, 1 + $level)));

                $places[] = [
                    'latitude'  => (float) strtr($row[$count - 3], ['N' => '', 'S' => '-', ',' => '.']),
                    'longitude' => (float) strtr($row[$count - 4], ['E' => '', 'W' => '-', ',' => '.']),
                    'name'      => $name
                ];
            }
        }

        fclose($fp);

        $added   = 0;
        $updated = 0;

        // Remove places with 0,0 coordinates at lower levels.
        $places = array_filter($places, static function ($place) {
            return !str_contains($place['name'], ',') || $place['longitude'] !== 0.0 || $place['latitude'] !== 0.0;
        });

        foreach ($places as $place) {
            $location = new PlaceLocation($place['name']);
            $exists   = $location->exists();

            // Only update existing records
            if ($options === 'update' && !$exists) {
                continue;
            }

            // Only add new records
            if ($options === 'add' && $exists) {
                continue;
            }

            if (!$exists) {
                $added++;
            }

            $updated += DB::table('place_location')
                ->where('id', '=', $location->id())
                ->update([
                    'latitude'  => $place['latitude'],
                    'longitude' => $place['longitude'],
                ]);
        }

        FlashMessages::addMessage(
            I18N::translate('locations updated: %s, locations added: %s', I18N::number($updated), I18N::number($added)),
            'info'
        );

        return redirect($url);
    }
}
