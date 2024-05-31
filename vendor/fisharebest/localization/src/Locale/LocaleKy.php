<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKy;

/**
 * Class LocaleKy - Kyrgyz
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleKy extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'кыргызча';
    }

    public function endonymSortable()
    {
        return 'КЫРГЫЗЧА';
    }

    public function language()
    {
        return new LanguageKy();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }
}
