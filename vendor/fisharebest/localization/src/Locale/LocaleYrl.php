<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageYrl;

/**
 * Class LocaleYrl - Nheengatu
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleYrl extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'nheẽgatu';
    }

    public function language()
    {
        return new LanguageYrl();
    }

    public function numberSymbols()
    {
        return array(
            self::DECIMAL => self::COMMA,
            self::GROUP   => self::DOT,
        );
    }
}
