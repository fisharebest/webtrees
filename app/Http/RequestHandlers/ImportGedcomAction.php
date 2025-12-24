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

use Fisharebest\Webtrees\Exceptions\FileUploadException;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Validator;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToReadFile;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function basename;
use function redirect;
use function route;

use const UPLOAD_ERR_NO_FILE;
use const UPLOAD_ERR_OK;

final class ImportGedcomAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly StreamFactoryInterface $stream_factory,
        private readonly TreeService $tree_service,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree               = Validator::attributes($request)->tree();
        $keep_media         = Validator::parsedBody($request)->boolean('keep_media', false);
        $word_wrapped_notes = Validator::parsedBody($request)->boolean('WORD_WRAPPED_NOTES', false);
        $gedcom_media_path  = Validator::parsedBody($request)->string('GEDCOM_MEDIA_PATH');
        $encodings          = ['' => ''] + Registry::encodingFactory()->list();
        $encoding           = Validator::parsedBody($request)->isInArrayKeys($encodings)->string('encoding');
        $source             = Validator::parsedBody($request)->isInArray(['client', 'server'])->string('source');

        // Save these choices as defaults
        $tree->setPreference('keep_media', $keep_media ? '1' : '0');
        $tree->setPreference('WORD_WRAPPED_NOTES', $word_wrapped_notes ? '1' : '0');
        $tree->setPreference('GEDCOM_MEDIA_PATH', $gedcom_media_path);

        if ($source === 'client') {
            $client_file = $request->getUploadedFiles()['client_file'] ?? null;

            if ($client_file === null || $client_file->getError() === UPLOAD_ERR_NO_FILE) {
                FlashMessages::addMessage(I18N::translate('No GEDCOM file was received.'), 'danger');

                return redirect(route(ImportGedcomPage::class, ['tree' => $tree->name()]));
            }

            if ($client_file->getError() !== UPLOAD_ERR_OK) {
                throw new FileUploadException($client_file);
            }

            $this->tree_service->importGedcomFile($tree, $client_file->getStream(), basename($client_file->getClientFilename()), $encoding);
        }

        if ($source === 'server') {
            $server_file = Validator::parsedBody($request)->string('server_file');

            if ($server_file === '') {
                FlashMessages::addMessage(I18N::translate('No GEDCOM file was received.'), 'danger');

                return redirect(route(ImportGedcomPage::class, ['tree' => $tree->name()]));
            }

            $resource = Registry::filesystem()->data()->readStream($server_file);
            $stream   = $this->stream_factory->createStreamFromResource($resource);
            $this->tree_service->importGedcomFile($tree, $stream, $server_file, $encoding);
        }

        return redirect(route(ManageTrees::class, ['tree' => $tree->name()]));
    }
}
