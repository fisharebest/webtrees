<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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
use Fisharebest\Webtrees\Services\PhpService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function date;
use function e;
use function pathinfo;
use function strtolower;
use function substr;

use const PATHINFO_EXTENSION;

/**
 * Show download forms/options.
 */
class ExportGedcomPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    public function __construct(private PhpService $php_service)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree  = Validator::attributes($request)->tree();
        $title = I18N::translate('Export a GEDCOM file') . ' — ' . e($tree->title());

        $this->layout = 'layouts/administration';

        $filename = $tree->name();

        // Force a ".ged" suffix
        if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'ged') {
            $download_filename  = substr($filename, 0, -4);
        } else {
            $download_filename = $filename;
        }

        $download_filenames = [
            $download_filename                  => $download_filename,
            $download_filename . date('-Y-m-d') => $download_filename . date('-Y-m-d'),
        ];

        return $this->viewResponse('admin/trees-export', [
            'download_filenames' => $download_filenames,
            'filename'           => $filename,
            'title'              => $title,
            'tree'               => $tree,
            'zip_available'      => $this->php_service->extensionLoaded('zip'),
        ]);
    }
}
