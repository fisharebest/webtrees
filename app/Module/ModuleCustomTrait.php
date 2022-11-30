<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

namespace Fisharebest\Webtrees\Module;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Mime;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Validator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function str_contains;
use function strtoupper;

/**
 * Trait ModuleCustomTrait - default implementation of ModuleCustomInterface
 */
trait ModuleCustomTrait
{
    /**
     * A unique internal name for this module (based on the installation folder).
     *
     * @return string
     */
    abstract public function name(): string;

    /**
     * Where does this module store its resources
     *
     * @return string
     */
    abstract public function resourcesFolder(): string;

    /**
     * The person or organisation who created this module.
     *
     * @return string
     */
    public function customModuleAuthorName(): string
    {
        return '';
    }

    /**
     * The version of this module.
     *
     * @return string  e.g. '1.2.3'
     */
    public function customModuleVersion(): string
    {
        return '';
    }

    /**
     * A URL that will provide the latest version of this module.
     *
     * @return string
     */
    public function customModuleLatestVersionUrl(): string
    {
        return '';
    }

    /**
     * Fetch the latest version of this module.
     *
     * @return string
     */
    public function customModuleLatestVersion(): string
    {
        // No update URL provided.
        if ($this->customModuleLatestVersionUrl() === '') {
            return $this->customModuleVersion();
        }

        return Registry::cache()->file()->remember($this->name() . '-latest-version', function (): string {
            try {
                $client = new Client([
                    'timeout' => 3,
                ]);

                $response = $client->get($this->customModuleLatestVersionUrl());

                if ($response->getStatusCode() === StatusCodeInterface::STATUS_OK) {
                    $version = $response->getBody()->getContents();

                    // Does the response look like a version?
                    if (preg_match('/^\d+\.\d+\.\d+/', $version)) {
                        return $version;
                    }
                }
            } catch (GuzzleException) {
                // Can't connect to the server?
            }

            return $this->customModuleVersion();
        }, 86400);
    }

    /**
     * Where to get support for this module.  Perhaps a github repository?
     *
     * @return string
     */
    public function customModuleSupportUrl(): string
    {
        return '';
    }

    /**
     * Additional/updated translations.
     *
     * @param string $language
     *
     * @return array<string,string>
     */
    public function customTranslations(string $language): array
    {
        return [];
    }

    /**
     * Create a URL for an asset.
     *
     * @param string $asset e.g. "css/theme.css" or "img/banner.png"
     *
     * @return string
     */
    public function assetUrl(string $asset): string
    {
        $file = $this->resourcesFolder() . $asset;

        // Add the file's modification time to the URL, so we can set long expiry cache headers.
        $hash = filemtime($file);

        return route('module', [
            'module' => $this->name(),
            'action' => 'Asset',
            'asset'  => $asset,
            'hash'   => $hash,
        ]);
    }

    /**
     * Serve a CSS/JS file.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getAssetAction(ServerRequestInterface $request): ResponseInterface
    {
        // The file being requested.  e.g. "css/theme.css"
        $asset = Validator::queryParams($request)->string('asset');

        // Do not allow requests that try to access parent folders.
        if (str_contains($asset, '..')) {
            throw new HttpAccessDeniedException($asset);
        }

        // Find the file for this asset.
        // Note that we could also generate CSS files using views/templates.
        // e.g. $file = view(....)
        $file = $this->resourcesFolder() . $asset;

        if (!file_exists($file)) {
            throw new HttpNotFoundException(e($file));
        }

        $content   = file_get_contents($file);
        $extension = strtoupper(pathinfo($asset, PATHINFO_EXTENSION));
        $mime_type = Mime::TYPES[$extension] ?? Mime::DEFAULT_TYPE;

        return response($content, StatusCodeInterface::STATUS_OK, [
            'cache-control'  => 'public,max-age=31536000',
            'content-type'   => $mime_type,
        ]);
    }
}
