<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageVec;

/**
 * Class LocaleIt - Italian
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleVec extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'veneto';
    }

    public function endonymSortable()
    {
        return 'VENETO';
    }

    public function language()
    {
        return new LanguageVec();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }
}
