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

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomExportService;
use Fisharebest\Webtrees\Validator;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToWriteFile;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function fclose;
use function pathinfo;
use function redirect;
use function route;
use function strtolower;

use const PATHINFO_EXTENSION;

/**
 * Save a GEDCOM file on the server.
 */
class ExportGedcomServer implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private GedcomExportService $gedcom_export_service;

    /**
     * ExportGedcomServer constructor.
     *
     * @param GedcomExportService $gedcom_export_service
     */
    public function __construct(GedcomExportService $gedcom_export_service)
    {
        $this->gedcom_export_service = $gedcom_export_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->tree();

        $data_filesystem = Registry::filesystem()->data();

        $filename = Validator::parsedBody($request)->string('filename');

        // Force a ".ged" suffix
        if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) !== 'ged') {
            $filename .= '.ged';
        }

        try {
            $resource = $this->gedcom_export_service->export($tree, true);
            $data_filesystem->writeStream($filename, $resource);
            fclose($resource);

            /* I18N: %s is a filename */
            FlashMessages::addMessage(I18N::translate('The family tree has been exported to %s.', Html::filename($filename)), 'success');
        } catch (FilesystemException | UnableToWriteFile $ex) {
            FlashMessages::addMessage(
                I18N::translate('The file %s could not be created.', Html::filename($filename)) . '<hr><samp dir="ltr">' . $ex->getMessage() . '</samp>',
                'danger'
            );
        }

        $url = route(ManageTrees::class, ['tree' => $tree->name()]);

        return redirect($url);
    }
}
