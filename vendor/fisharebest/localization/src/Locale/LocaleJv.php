<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageJv;

/**
 * Class LocaleJv - Javanese
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleJv extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Jawa';
    }

    public function language()
    {
        return new LanguageJv();
    }

    public function numberSymbols()
    {
        return array(
            self::DECIMAL => self::COMMA,
            self::GROUP   => self::DOT,
        );
    }
}
