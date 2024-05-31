<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNr;

/**
 * Class LocaleNr - South Ndebele
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleNr extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'isiNdebele';
    }

    public function endonymSortable()
    {
        return 'ISINDEBELE';
    }

    public function language()
    {
        return new LanguageNr();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }
}
