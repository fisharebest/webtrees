<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryFi;

/**
 * Class LocaleEnDe - English
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEnFi extends LocaleEn
{
    public function numberSymbols()
    {
        return array(
            self::GROUP => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::NBSP . self::PERCENT;
    }

    public function territory()
    {
        return new TerritoryFi();
    }
}
