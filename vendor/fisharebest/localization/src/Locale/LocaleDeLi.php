<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryLi;

/**
 * Class LocaleDeLi
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleDeLi extends LocaleDe
{
    public function territory()
    {
        return new TerritoryLi();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::APOSTROPHE,
            self::DECIMAL => self::DOT,
        );
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::PERCENT;
    }
}
