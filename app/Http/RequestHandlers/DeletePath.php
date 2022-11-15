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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Validator;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\WhitespacePathNormalizer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function e;
use function in_array;
use function response;
use function str_ends_with;

/**
 * Delete a file or folder from the data filesystem.
 */
class DeletePath implements RequestHandlerInterface
{
    private const PROTECTED_PATHS = [
        'config.ini.php',
        'index.php',
        '.htaccess',
    ];

    private WhitespacePathNormalizer $whitespace_path_normalizer;

    /**
     * @param WhitespacePathNormalizer $whitespace_path_normalizer
     */
    public function __construct(WhitespacePathNormalizer $whitespace_path_normalizer)
    {
        $this->whitespace_path_normalizer = $whitespace_path_normalizer;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data_filesystem = Registry::filesystem()->data();

        $path = Validator::queryParams($request)->string('path');

        $normalized_path = $this->whitespace_path_normalizer->normalizePath($path);

        if (in_array($normalized_path, self::PROTECTED_PATHS, true)) {
            FlashMessages::addMessage(I18N::translate('The file %s could not be deleted.', e($path)), 'danger');
            return response();
        }

        // The request adds a slash to folders, so we know which delete function to use.
        if (str_ends_with($path, '/')) {
            try {
                $data_filesystem->deleteDirectory($normalized_path);
                FlashMessages::addMessage(I18N::translate('The folder %s has been deleted.', e($path)), 'success');
            } catch (FilesystemException | UnableToDeleteDirectory $ex) {
                FlashMessages::addMessage(I18N::translate('The folder %s could not be deleted.', e($path)), 'danger');
            }
        } else {
            try {
                $data_filesystem->delete($normalized_path);
                FlashMessages::addMessage(I18N::translate('The file %s has been deleted.', e($path)), 'success');
            } catch (FilesystemException | UnableToDeleteFile $ex) {
                FlashMessages::addMessage(I18N::translate('The file %s could not be deleted.', e($path)), 'danger');
            }
        }

        return response();
    }
}
