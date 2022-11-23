<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKm;

/**
 * Class LocaleKm - Khmer
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleKm extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'ខ្មែរ';
    }

    public function language()
    {
        return new LanguageKm();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }
}
