<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptLatn;
use Fisharebest\Localization\Territory\TerritoryDz;

/**
 * Class LocaleArDz
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleArDz extends LocaleAr
{
    public function numberSymbols()
    {
        return array(
            self::GROUP    => self::DOT,
            self::DECIMAL  => self::COMMA,
            self::NEGATIVE => self::LTR_MARK . self::HYPHEN,
        );
    }

    protected function numerals()
    {
        $latin = new ScriptLatn();

        return $latin->numerals();
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::LTR_MARK . self::PERCENT . self::LTR_MARK;
    }

    public function territory()
    {
        return new TerritoryDz();
    }
}
