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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\AdminService;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Services\TreeService;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;
use function route;

/**
 * Synchronize GEDCOM files with trees.
 */
class SynchronizeTrees implements RequestHandlerInterface
{
    private AdminService $admin_service;

    private StreamFactoryInterface $stream_factory;

    private TimeoutService $timeout_service;

    private TreeService $tree_service;

    /**
     * AdminTreesController constructor.
     *
     * @param AdminService        $admin_service
     * @param StreamFactoryInterface $stream_factory
     * @param TimeoutService      $timeout_service
     * @param TreeService         $tree_service
     */
    public function __construct(
        AdminService $admin_service,
        StreamFactoryInterface $stream_factory,
        TimeoutService $timeout_service,
        TreeService $tree_service
    ) {
        $this->admin_service   = $admin_service;
        $this->stream_factory  = $stream_factory;
        $this->timeout_service = $timeout_service;
        $this->tree_service    = $tree_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data_filesystem = Registry::filesystem()->data();

        $gedcom_files = $this->admin_service->gedcomFiles($data_filesystem);

        foreach ($gedcom_files as $gedcom_file) {
            // Only import files that have changed
            try {
                $filemtime = (string) $data_filesystem->lastModified($gedcom_file);

                $tree = $this->tree_service->all()->get($gedcom_file) ?? $this->tree_service->create($gedcom_file, $gedcom_file);

                if ($tree->getPreference('filemtime') !== $filemtime) {
                    $resource = $data_filesystem->readStream($gedcom_file);
                    $stream   = $this->stream_factory->createStreamFromResource($resource);
                    $this->tree_service->importGedcomFile($tree, $stream, $gedcom_file, '');
                    $stream->close();
                    $tree->setPreference('filemtime', $filemtime);

                    FlashMessages::addMessage(I18N::translate('The GEDCOM file “%s” has been imported.', e($gedcom_file)), 'success');

                    if ($this->timeout_service->isTimeNearlyUp(10.0)) {
                        return redirect(route(self::class), StatusCodeInterface::STATUS_TEMPORARY_REDIRECT);
                    }
                }
            } catch (FilesystemException | UnableToRetrieveMetadata | UnableToReadFile) {
                // Can't read the file - skip it.
            }
        }

        foreach ($this->tree_service->all() as $tree) {
            if (!$gedcom_files->containsStrict($tree->name())) {
                $this->tree_service->delete($tree);
                FlashMessages::addMessage(I18N::translate('The family tree “%s” has been deleted.', e($tree->title())), 'success');

                if ($this->timeout_service->isTimeNearlyUp(10.0)) {
                    return redirect(route(self::class), StatusCodeInterface::STATUS_TEMPORARY_REDIRECT);
                }
            }
        }

        return redirect(route(ManageTrees::class, ['tree' => $this->tree_service->all()->first()->name()]));
    }
}
