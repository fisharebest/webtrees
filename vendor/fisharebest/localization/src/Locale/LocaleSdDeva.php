<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptDeva;

/**
 * Class LocaleSdDeva - Sindhi
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSdDeva extends LocaleSd
{
    public function direction()
    {
        return 'rtl';
    }

    public function script()
    {
        return new ScriptDeva();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP    => self::COMMA,
            self::DECIMAL  => self::DOT,
            self::NEGATIVE => self::HYPHEN,
        );
    }

    protected function percentFormat()
    {
        return AbstractLocale::PLACEHOLDER . self::PERCENT;
    }
}
