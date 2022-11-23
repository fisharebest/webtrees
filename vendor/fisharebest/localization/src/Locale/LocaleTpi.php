<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTpi;

/**
 * Class LocaleTpi - Tok Pisin
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleTpi extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Tok Pisin';
    }

    public function language()
    {
        return new LanguageTpi();
    }

    public function numberSymbols()
    {
        return array(
            self::DECIMAL => self::COMMA,
            self::GROUP   => self::DOT,
        );
    }
}
