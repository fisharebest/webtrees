<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageDsb;

/**
 * Class LocaleDsb - Lower Sorbian
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleDsb extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'dolnoserbšćina';
    }

    public function endonymSortable()
    {
        return 'DOLNOSERBSCINA';
    }

    public function language()
    {
        return new LanguageDsb();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::NBSP . self::PERCENT;
    }
}
