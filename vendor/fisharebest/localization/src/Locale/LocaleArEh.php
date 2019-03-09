<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptLatn;
use Fisharebest\Localization\Territory\TerritoryEh;

/**
 * Class LocaleArEh
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocaleArEh extends LocaleAr
{
    public function numberSymbols()
    {
        return array(
            self::NEGATIVE => self::LTR_MARK . '-',
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
        return new TerritoryEh();
    }
}
