<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTg;

/**
 * Class LocaleTg - Tajik
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleTg extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'тоҷикӣ';
    }

    public function language()
    {
        return new LanguageTg();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }
}
