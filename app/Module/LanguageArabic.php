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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\I18N\Languages\Arabic;

class LanguageArabic extends AbstractModule implements ModuleLanguageInterface
{
    use ModuleLanguageTrait;

    public function __construct()
    {
        $this->language = new Arabic();
    }
    /**
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        // Issue #5262 - the INTL library doesn't convert these.
        return [
            UTF8::ARABIC_LETTER_TEH_MARBUTA  => UTF8::ARABIC_LETTER_TEH,
            UTF8::ARABIC_LETTER_ALEF_MAKSURA => UTF8::ARABIC_LETTER_YEH,
            UTF8::ARABIC_LETTER_ALEF_WASLA   => UTF8::ARABIC_LETTER_ALEF,
        ];
    }
}
