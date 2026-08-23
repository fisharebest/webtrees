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

/**
 * Converts between domain objects and scalar values for route parameters.
 *
 * Implementations handle specific domain types — serializing them to scalars
 * for URL generation, and deserializing scalars back to domain objects for
 * controller dispatch.
 */
interface ParameterResolverInterface
{
    /**
     * Can this resolver handle the given type name?
     *
     * @param string $type_name The fully-qualified class name or scalar type name.
     */
    public function supports(string $type_name): bool;

    /**
     * Serialize a domain object to a scalar value suitable for a URL.
     *
     * @return bool|int|string|array<string>|null
     */
    public function serialize(mixed $value): bool|int|string|array|null;

    /**
     * Deserialize a scalar value from a request into a domain object.
     *
     * @param array<string, mixed> $context Additional context (e.g. resolved Tree for GEDCOM records).
     */
    public function deserialize(string $value, string $type_name, array $context = []): mixed;
}
