<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageIa;

/**
 * Class LocaleIa - Interlingua
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleIa extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'interlingua';
    }

    public function endonymSortable()
    {
        return 'INTERLINGUA';
    }

    public function language()
    {
        return new LanguageIa();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }
}
