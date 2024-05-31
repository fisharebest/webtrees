<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageUr;

/**
 * Class LocaleUr - Urdu
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleUr extends AbstractLocale implements LocaleInterface
{
    protected function digitsGroup()
    {
        return 2;
    }

    public function endonym()
    {
        return 'اردو';
    }

    public function language()
    {
        return new LanguageUr();
    }

    public function numberSymbols()
    {
        return array(
            self::NEGATIVE => self::LTR_MARK . self::HYPHEN,
        );
    }
}
