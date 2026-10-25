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
 * Aggregates multiple ParameterResolver implementations.
 * Delegates to the first resolver that supports the given type.
 */
final class ParameterResolver implements ParameterResolverInterface
{
    /** @var list<ParameterResolverInterface> */
    private array $resolvers = [];

    /**
     * @param list<ParameterResolverInterface> $resolvers
     */
    public function __construct(array $resolvers = [])
    {
        $this->resolvers = $resolvers;
    }

    public function add(ParameterResolverInterface $resolver): self
    {
        $this->resolvers[] = $resolver;

        return $this;
    }

    public function supports(string $type_name): bool
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->supports($type_name)) {
                return true;
            }
        }

        return false;
    }

    public function serialize(mixed $value): bool|int|string|array|null
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->supports($value::class)) {
                return $resolver->serialize($value);
            }
        }

        return $value;
    }

    public function deserialize(string $value, string $type_name, array $context = []): mixed
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->supports($type_name)) {
                return $resolver->deserialize($value, $type_name, $context);
            }
        }

        return null;
    }
}
