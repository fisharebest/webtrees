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

namespace Fisharebest\Webtrees\I18N;

use Fisharebest\Webtrees\Enums\PluralRule;

final class Translator
{
    /**
     * @param array<string,string> $translations
     */
    public function __construct(
        private array $translations,
        private PluralRule $plural_rule,
    ) {
    }

    public function translate(string $message): string
    {
        return $this->translations[$message] ?? $message;
    }

    public function translateContext(string $context, string $message): string
    {
        return $this->translations[$context . Translation::CONTEXT_SEPARATOR . $message] ?? $message;
    }

    public function translatePlural(string $singular, string $plural, int $number): string
    {
        $key = $singular . Translation::PLURAL_SEPARATOR . $plural;

        if (isset($this->translations[$key])) {
            $plurals = explode(Translation::PLURAL_SEPARATOR, $this->translations[$key]);

            return $plurals[$this->plural_rule->plural($number)];
        }

        return $number === 1 ? $singular : $plural;
    }
}
