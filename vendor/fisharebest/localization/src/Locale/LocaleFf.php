<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageFf;

/**
 * Class LocaleFf - Fulah
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleFf extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Pulaar';
    }

    public function endonymSortable()
    {
        return 'PULAAR';
    }

    public function language()
    {
        return new LanguageFf();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }
}
