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

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tree;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToReadFile;
use Nyholm\Psr7\UploadedFile;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function basename;
use function redirect;
use function route;

use const UPLOAD_ERR_OK;

/**
 * Import a GEDCOM file into a tree.
 */
class ImportGedcomAction implements RequestHandlerInterface
{
    private StreamFactoryInterface $stream_factory;

    private TreeService $tree_service;

    public function __construct(StreamFactoryInterface $stream_factory, TreeService $tree_service)
    {
        $this->tree_service   = $tree_service;
        $this->stream_factory = $stream_factory;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws FilesystemException
     * @throws UnableToReadFile
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $data_filesystem = Registry::filesystem()->data();

        $params             = (array) $request->getParsedBody();
        $source             = $params['source'];
        $keep_media         = (bool) ($params['keep_media'] ?? false);
        $WORD_WRAPPED_NOTES = (bool) ($params['WORD_WRAPPED_NOTES'] ?? false);
        $GEDCOM_MEDIA_PATH  = $params['GEDCOM_MEDIA_PATH'];

        // Save these choices as defaults
        $tree->setPreference('keep_media', $keep_media ? '1' : '0');
        $tree->setPreference('WORD_WRAPPED_NOTES', $WORD_WRAPPED_NOTES ? '1' : '0');
        $tree->setPreference('GEDCOM_MEDIA_PATH', $GEDCOM_MEDIA_PATH);

        if ($source === 'client') {
            $upload = $request->getUploadedFiles()['tree_name'] ?? null;

            if ($upload instanceof UploadedFile) {
                if ($upload->getError() === UPLOAD_ERR_OK) {
                    $this->tree_service->importGedcomFile($tree, $upload->getStream(), basename($upload->getClientFilename()));
                } else {
                    FlashMessages::addMessage(Functions::fileUploadErrorText($upload->getError()), 'danger');
                }
            } else {
                FlashMessages::addMessage(I18N::translate('No GEDCOM file was received.'), 'danger');
            }
        }

        if ($source === 'server') {
            $basename = basename($params['tree_name'] ?? '');

            if ($basename) {
                $resource = $data_filesystem->readStream($basename);
                $stream   = $this->stream_factory->createStreamFromResource($resource);
                $this->tree_service->importGedcomFile($tree, $stream, $basename);
            } else {
                FlashMessages::addMessage(I18N::translate('No GEDCOM file was received.'), 'danger');
            }
        }

        $url = route(ManageTrees::class, ['tree' => $tree->name()]);

        return redirect($url);
    }
}
