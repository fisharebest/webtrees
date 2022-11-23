<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKgp;

/**
 * Class LocaleKgp - Kaingang
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleKgp extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'kanhgág';
    }

    public function language()
    {
        return new LanguageKgp();
    }

    public function numberSymbols()
    {
        return array(
            self::DECIMAL => self::COMMA,
            self::GROUP   => self::DOT,
        );
    }
}
