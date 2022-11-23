<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKsf;

/**
 * Class LocaleKsf - Bafia
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleKsf extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'rikpa';
    }

    public function endonymSortable()
    {
        return 'RIKPA';
    }

    public function language()
    {
        return new LanguageKsf();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }
}
