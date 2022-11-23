<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptArab;

/**
 * Class LocaleUzArab
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleUzArab extends LocaleUz
{
    public function script()
    {
        return new ScriptArab();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP    => self::ARAB_GROUP,
            self::DECIMAL  => self::ARAB_DECIMAL,
            self::NEGATIVE => self::HYPHEN,
        );
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::ARAB_PERCENT;
    }
}
