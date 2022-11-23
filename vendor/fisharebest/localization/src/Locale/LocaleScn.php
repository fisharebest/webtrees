<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageScn;

/**
 * Class LocaleScn - Sicilain
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleScn extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Sicilianu';
    }

    public function endonymSortable()
    {
        return 'SICILIANU';
    }

    public function language()
    {
        return new LanguageScn();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }
}
