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
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\MapProviderService;
use Fisharebest\Webtrees\Site;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Str;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function fclose;
use function json_decode;
use function preg_replace;
use function redirect;
use function route;
use function serialize;
use function stream_get_contents;

use const JSON_THROW_ON_ERROR;
use const UPLOAD_ERR_OK;

/**
 * Import a map provider.
 */
class MapProviderImportAction implements RequestHandlerInterface
{
    /**
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data_filesystem = Registry::filesystem()->data();
        $params          = (array) $request->getParsedBody();
        $serverfile      = $params['serverfile'] ?? '';
        $local_file      = $request->getUploadedFiles()['localfile'] ?? null;
        $url             = route(MapProviderPage::class);
        $fp              = false;

        if ($serverfile !== '' && $data_filesystem->fileExists(MapProviderService::PROVIDER_FOLDER . $serverfile)) {
            // first choice is file on server
            $fp = $data_filesystem->readStream(MapProviderService::PROVIDER_FOLDER . $serverfile);
        } elseif ($local_file instanceof UploadedFileInterface && $local_file->getError() === UPLOAD_ERR_OK) {
            // 2nd choice is local file
            $fp = $local_file->getStream()->detach();
        }

        if ($fp === false || $fp === null) {
            return redirect($url);
        }

        $json = stream_get_contents($fp);
        fclose($fp);

        try {
            assert($json !== false);

            $string = json_decode($json, false, 512, JSON_THROW_ON_ERROR);

            $key_name = $this->canonical($string->title);
            $data     = [];

            DB::table('map_names')
                ->where('key_name', '=', $key_name)
                ->delete();

            $id = DB::table('map_names')
                ->insertGetId([
                    'display_name' => $string->title,
                    'key_name'     => $key_name,
                ]);

            foreach ($string->user as $name => $value) {
                $data[] = [
                    'parent_id'       => $id,
                    'type'            => 'user',
                    'parameter_name'  => $name,
                    'parameter_value' => serialize($value),
                ];
            }


            foreach ($string->common as $name => $value) {
                $data[] = [
                    'parent_id'       => $id,
                    'type'            => 'common',
                    'parameter_name'  => $name,
                    'parameter_value' => serialize($value),
                ];
            }

            foreach ($string->styles as $style) {
                $style_id = DB::table('map_names')
                    ->insertGetId([
                        'display_name' => $style->title,
                        'key_name'     => $this->canonical($style->title),
                        'provider_id'  => $id,
                    ]);

                foreach ($style->parameters as $name => $value) {
                    $data[] = [
                        'parent_id'       => $style_id,
                        'type'            => 'style',
                        'parameter_name'  => $name,
                        'parameter_value' => serialize($value),
                    ];
                }
            }

            DB::table('map_parameters')
                ->insert($data);

            Site::setPreference($key_name . '-enabled', '0');
        } catch (JsonException $ex) {
            FlashMessages::addMessage(I18N::translate('Provider import: %s', $ex->getMessage()), 'info');
        }

        return redirect($url);
    }

    /**
     *
     * @param string $text
     * @return string
     */
    private function canonical(string $text): string
    {
        $text = preg_replace('/[^[:alnum:]]/u', '', $text);

        return Str::ascii(Str::lower($text ?? str::random()));
    }
}
