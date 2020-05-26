<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use League\Flysystem\FilesystemInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function is_string;
use function redirect;

/**
 * Show a media object's page.
 */
class MediaPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /** @var ClipboardService */
    private $clipboard_service;

    /**
     * MediaPage constructor.
     *
     * @param ClipboardService $clipboard_service
     */
    public function __construct(ClipboardService $clipboard_service)
    {
        $this->clipboard_service = $clipboard_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data_filesystem = $request->getAttribute('filesystem.data');
        assert($data_filesystem instanceof FilesystemInterface);

        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref = $request->getAttribute('xref');
        assert(is_string($xref));

        $media = Factory::media()->make($xref, $tree);
        $media = Auth::checkMediaAccess($media);

        // Redirect to correct xref/slug
        if ($media->xref() !== $xref || $request->getAttribute('slug') !== $media->slug()) {
            return redirect($media->url(), StatusCodeInterface::STATUS_MOVED_PERMANENTLY);
        }

        return $this->viewResponse('media-page', [
            'clipboard_facts'  => $this->clipboard_service->pastableFacts($media, new Collection()),
            'data_filesystem'  => $data_filesystem,
            'families'         => $media->linkedFamilies('OBJE'),
            'facts'            => $this->facts($media),
            'individuals'      => $media->linkedIndividuals('OBJE'),
            'media'            => $media,
            'meta_description' => '',
            'meta_robots'      => 'index,follow',
            'notes'            => $media->linkedNotes('OBJE'),
            'sources'          => $media->linkedSources('OBJE'),
            'title'            => $media->fullName(),
            'tree'             => $tree,
        ]);
    }

    /**
     * @param Media $record
     *
     * @return Collection<Fact>
     */
    private function facts(Media $record): Collection
    {
        return $record->facts()
            ->filter(static function (Fact $fact): bool {
                return $fact->tag() !== 'FILE';
            });
    }
}
