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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ReorderMediaFilesPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree  = Validator::attributes($request)->tree();
        $xref  = Validator::attributes($request)->isXref()->string('xref');
        $media = Registry::mediaFactory()->make($xref, $tree);
        $media = Auth::checkMediaAccess($media, true);
        $title = $media->fullName() . ' — ' . I18N::translate('Re-order media');

        if ($media->mediaFiles()->count() < 2) {
            return Registry::responseFactory()->redirect(MediaPage::class, [
                'tree' => $tree->name(),
                'xref' => $media->xref(),
            ]);
        }

        return $this->viewResponse('edit/reorder-media-files', [
            'media' => $media,
            'title' => $title,
            'tree'  => $tree,
        ]);
    }
}
