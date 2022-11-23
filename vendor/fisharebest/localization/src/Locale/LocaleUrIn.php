<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryIn;

/**
 * Class LocaleUrIn
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleUrIn extends LocaleUr
{
    public function territory()
    {
        return new TerritoryIn();
    }

    public function numberSymbols()
    {
        return array(
            self::DECIMAL  => self::ARAB_DECIMAL,
            self::GROUP    => self::ARAB_GROUP,
            self::NEGATIVE => self::LTR_MARK . self::HYPHEN . self::LTR_MARK,
        );
    }
}
