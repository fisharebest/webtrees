<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
use Fisharebest\Webtrees\Services\AdminService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function e;

/**
 * Import a GEDCOM file into a tree.
 */
class ImportGedcomPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private AdminService $admin_service;

    /**
     * @param AdminService $admin_service
     */
    public function __construct(AdminService $admin_service)
    {
        $this->admin_service = $admin_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $tree = Validator::attributes($request)->tree();

        $data_filesystem = Registry::filesystem()->data();
        $data_folder     = Registry::filesystem()->dataName();

        $default_gedcom_file = $tree->getPreference('gedcom_filename');
        $gedcom_media_path   = $tree->getPreference('GEDCOM_MEDIA_PATH');
        $gedcom_files        = $this->admin_service->gedcomFiles($data_filesystem);

        $title = I18N::translate('Import a GEDCOM file') . ' — ' . e($tree->title());

        return $this->viewResponse('admin/trees-import', [
            'data_folder'         => $data_folder,
            'default_gedcom_file' => $default_gedcom_file,
            'gedcom_files'        => $gedcom_files,
            'gedcom_media_path'   => $gedcom_media_path,
            'title'               => $title,
            'tree'                => $tree,
        ]);
    }
}
