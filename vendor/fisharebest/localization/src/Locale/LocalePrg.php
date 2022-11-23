<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguagePrg;

/**
 * Class LocalePrg - Old Prussian
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocalePrg extends AbstractLocale implements LocaleInterface
{
    public function collation()
    {
        return 'latvian_ci';
    }

    public function endonym()
    {
        return 'prūsiskan';
    }

    public function endonymSortable()
    {
        return 'PRŪSISKAN';
    }

    public function language()
    {
        return new LanguagePrg();
    }

    protected function minimumGroupingDigits()
    {
        return 3;
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }
}
