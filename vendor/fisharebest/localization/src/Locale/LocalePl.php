<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguagePl;

/**
 * Class LocalePl - Polish
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocalePl extends AbstractLocale implements LocaleInterface
{
    public function collation()
    {
        return 'polish_ci';
    }

    public function endonym()
    {
        return 'polski';
    }

    public function endonymSortable()
    {
        return 'POLSKI';
    }

    public function language()
    {
        return new LanguagePl();
    }

    protected function minimumGroupingDigits()
    {
        return 2;
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }
}
