<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKea;

/**
 * Class LocaleKea - Kabuverdianu
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleKea extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'kabuverdianu';
    }

    public function endonymSortable()
    {
        return 'KABUVERDIANU';
    }

    public function language()
    {
        return new LanguageKea();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }
}
