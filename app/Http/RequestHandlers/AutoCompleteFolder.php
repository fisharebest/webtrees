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

use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ServerRequestInterface;

use function assert;

/**
 * Autocomplete handler for media folders
 */
class AutoCompleteFolder extends AbstractAutocompleteHandler
{
    protected function search(ServerRequestInterface $request): Collection
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $query = $request->getAttribute('query');

        $media_filesystem = Registry::filesystem()->media($tree);

        $contents = new Collection($media_filesystem->listContents('', true));

        return $contents
            ->filter(static function (array $object) use ($query): bool {
                return $object['type'] === 'dir' && str_contains($object['path'], $query);
            })
            ->values()
            ->pluck('path')
            ->take(static::LIMIT);
    }
}
