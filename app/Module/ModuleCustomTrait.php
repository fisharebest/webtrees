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

namespace Fisharebest\Webtrees\Module;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\Http\RequestHandlers\ModuleAction;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function strlen;

/**
 * Trait ModuleCustomTrait - default implementation of ModuleCustomInterface
 */
trait ModuleCustomTrait
{
    /**
     * Where does this module store its resources
     *
     * @return string
     */
    abstract public function resourcesFolder(): string;

    /**
     * A unique internal name for this module (based on the installation folder).
     *
     * @return string
     */
    abstract public function name(): string;

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
     * Where to get support for this module.  Perhaps a github respository?
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
     * @return string[]
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
            'action' => 'asset',
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
        $asset = $request->getQueryParams()['asset'];

        // Do not allow requests that try to access parent folders.
        if (Str::contains($asset, '..')) {
            throw new AccessDeniedHttpException($asset);
        }

        // Find the file for this asset.
        // Note that we could also generate CSS files using views/templates.
        // e.g. $file = view(....
        $file = $this->resourcesFolder() . $asset;

        if (!file_exists($file)) {
            throw new NotFoundHttpException($file);
        }

        $content   = file_get_contents($file);
        $extension = pathinfo($asset, PATHINFO_EXTENSION);

        $mime_types = [
            'css'  => 'text/css',
            'gif'  => 'image/gif',
            'js'   => 'application/javascript',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'json' => 'application/json',
            'png'  => 'image/png',
            'txt'  => 'text/plain',
        ];

        $mime_type = $mime_types[$extension] ?? 'application/octet-stream';

        $headers = [
            'Content-Type'   => $mime_type,
            'Cache-Control'  => 'max-age=31536000, public',
            'Content-Length' => strlen($content),
            'Expires'        => Carbon::now()->addYears(10)->toRfc7231String(),
        ];

        return response($content, StatusCodeInterface::STATUS_OK, $headers);
    }
}
