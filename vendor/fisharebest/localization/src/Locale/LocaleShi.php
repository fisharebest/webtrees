<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageShi;

/**
 * Class LocaleShi - Tachelhit
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleShi extends AbstractLocale implements LocaleInterface
{
    public function direction()
    {
        return 'ltr';
    }

    public function endonym()
    {
        return 'ⵜⴰⵛⵍⵃⵉⵜ';
    }

    public function language()
    {
        return new LanguageShi();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }
}
