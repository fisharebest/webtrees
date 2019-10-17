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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use InvalidArgumentException;
use League\Flysystem\FilesystemInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

use function assert;
use function e;
use function is_string;
use function response;

/**
 * Delete a file.
 */
class DeletePath implements RequestHandlerInterface, StatusCodeInterface
{
    /** @var FilesystemInterface */
    private $filesystem;

    /**
     * @param FilesystemInterface $filesystem
     */
    public function __construct(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $path = $request->getQueryParams()['path'];
        assert(is_string($path), new InvalidArgumentException());

        if ($this->filesystem->has($path)) {
            $metadata = $this->filesystem->getMetadata($path);

            switch ($metadata['type']) {
                case 'file':
                    try {
                        $this->filesystem->delete($path);
                        FlashMessages::addMessage(I18N::translate('The file %s has been deleted.', e($path)), 'success');
                    } catch (Throwable $ex) {
                        FlashMessages::addMessage(I18N::translate('The file %s could not be deleted.', e($path)), 'danger');
                    }
                    break;

                case 'dir':
                    try {
                        $this->filesystem->deleteDir($path);
                        FlashMessages::addMessage(I18N::translate('The folder %s has been deleted.', e($path)), 'success');
                    } catch (Throwable $ex) {
                        FlashMessages::addMessage(I18N::translate('The folder %s could not be deleted.', e($path)), 'danger');
                    }
                    break;
            }
        } else {
            FlashMessages::addMessage(I18N::translate('The file %s could not be deleted.', e($path)), 'danger');
        }

        return response('', StatusCodeInterface::STATUS_NO_CONTENT);
    }
}
