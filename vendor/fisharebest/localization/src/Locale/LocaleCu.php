<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageCu;

/**
 * Class LocaleCu - Old Church Slavonic
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleCu extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'церковнослове́нскїй';
    }

    public function endonymSortable()
    {
        return 'ЦЕРКОВНОСЛОВЕ́НСКЇЙ';
    }

    public function language()
    {
        return new LanguageCu();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::NBSP . self::PERCENT;
    }
}
