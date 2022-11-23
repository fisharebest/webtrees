<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNl;

/**
 * Class LocaleNl - Dutch
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleNl extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Nederlands';
    }

    public function endonymSortable()
    {
        return 'NEDERLANDS';
    }

    public function language()
    {
        return new LanguageNl();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }
}
