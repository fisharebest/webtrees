<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBn;

/**
 * Class LocaleMsMy
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleMsBn extends LocaleMs
{
    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }

    public function territory()
    {
        return new TerritoryBn();
    }
}
