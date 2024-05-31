<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageAz;

/**
 * Class LocaleAz - Azerbaijani
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleAz extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'azərbaycan';
    }

    public function endonymSortable()
    {
        return 'AZERBAYCAN';
    }

    public function language()
    {
        return new LanguageAz();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }
}
