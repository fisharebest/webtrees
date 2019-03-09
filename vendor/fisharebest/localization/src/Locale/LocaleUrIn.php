<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryIn;

/**
 * Class LocaleUrIn
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
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
