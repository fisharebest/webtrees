<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Http\Routing;

use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tree;

/**
 * Resolves Tree parameters: serializes to tree name, deserializes from tree name.
 */
final readonly class TreeParameterResolver implements ParameterResolverInterface
{
    public function __construct(
        private readonly TreeService $tree_service,
    ) {
    }

    public function supports(string $type_name): bool
    {
        return $type_name === Tree::class;
    }

    public function serialize(mixed $value): string|null
    {
        if ($value instanceof Tree) {
            return $value->name();
        }

        return null;
    }

    public function deserialize(string $value, string $type_name, array $context = []): Tree|null
    {
        return $this->tree_service->all()->get($value);
    }
}
