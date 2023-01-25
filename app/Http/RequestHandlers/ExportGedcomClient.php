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

use Fisharebest\Webtrees\Encodings\ANSEL;
use Fisharebest\Webtrees\Encodings\ASCII;
use Fisharebest\Webtrees\Encodings\UTF16BE;
use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\Encodings\Windows1252;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Services\GedcomExportService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Download a GEDCOM file to the client.
 */
class ExportGedcomClient implements RequestHandlerInterface
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
        $tree         = Validator::attributes($request)->tree();
        $filename     = Validator::parsedBody($request)->string('filename');
        $format       = Validator::parsedBody($request)->isInArray(['gedcom', 'zip', 'zipmedia', 'gedzip'])->string('format');
        $privacy      = Validator::parsedBody($request)->isInArray(['none', 'gedadmin', 'user', 'visitor'])->string('privacy');
        $encoding     = Validator::parsedBody($request)->isInArray([UTF8::NAME, UTF16BE::NAME, ANSEL::NAME, ASCII::NAME, Windows1252::NAME])->string('encoding');
        $line_endings = Validator::parsedBody($request)->isInArray(['CRLF', 'LF'])->string('line_endings');

        return $this->gedcom_export_service->downloadResponse($tree, true, $encoding, $privacy, $line_endings, $filename, $format);
    }
}
