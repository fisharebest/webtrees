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

namespace Fisharebest\Webtrees;

use ArrayObject;
use Closure;

use function array_filter;
use function array_map;
use function array_merge;
use function array_unique;
use function array_values;
use function uasort;

/**
 * Arrays
 *
 * @template TKey of array-key
 * @template TValue
 * @extends  ArrayObject<TKey,TValue>
 */
class Arr extends ArrayObject
{
    /**
     * @param Arr<TKey,TValue> $arr2
     *
     * @return self<TKey,TValue>
     */
    public function concat(Arr $arr2): self
    {
        $arr1 = array_values(array: $this->getArrayCopy());
        $arr2 = array_values(array: $arr2->getArrayCopy());

        return new self(array: $arr1 + $arr2);
    }

    /**
     * @param Closure(TValue):bool $closure
     *
     * @return self<TKey,TValue>
     */
    public function filter(Closure $closure): self
    {
        return new self(array: array_filter(array: $this->getArrayCopy(), callback: $closure));
    }

    /**
     * @return self<int,TValue>
     */
    public function flatten(): self
    {
        return new self(array: array_merge(...$this->getArrayCopy()));
    }

    /**
     * @param null|Closure(TValue):bool $closure
     *
     * @return TValue|null
     */
    public function first(Closure|null $closure = null): mixed
    {
        foreach ($this->getArrayCopy() as $value) {
            if ($closure === null || $closure($value)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param null|Closure(TValue):bool $closure
     *
     * @return TValue|null
     */
    public function last(Closure|null $closure = null): mixed
    {
        return $this->reverse()->first(closure: $closure);
    }

    /**
     * @template T
     *
     * @param Closure(TValue):T $closure
     *
     * @return self<TKey,T>
     */
    public function map(Closure $closure): self
    {
        return new self(array: array_map(callback: $closure, array: $this->getArrayCopy()));
    }

    /**
     * @return self<TKey,TValue>
     */
    public function reverse(): self
    {
        return new self(array: array_reverse(array: $this->getArrayCopy()));
    }

    /**
     * @param Closure(TValue,TValue):int $closure
     *
     * @return self<TKey,TValue>
     */
    public function sort(Closure $closure): self
    {
        $arr = $this->getArrayCopy();
        uasort(array: $arr, callback: $closure);

        return new self(array: $arr);
    }

    /**
     * @param Closure(TKey,TKey):int $closure
     *
     * @return self<TKey,TValue>
     */
    public function sortKeys(Closure $closure): self
    {
        $arr = $this->getArrayCopy();
        uksort(array: $arr, callback: $closure);

        return new self(array: $arr);
    }

    /**
     * @return self<TKey,TValue>
     */
    public function unique(): self
    {
        return new self(array: array_unique(array: $this->getArrayCopy()));
    }
}
