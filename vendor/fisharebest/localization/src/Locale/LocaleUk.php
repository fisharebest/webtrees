<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageUk;

/**
 * Class LocaleUk - Ukrainian
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleUk extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'українська';
    }

    public function endonymSortable()
    {
        return 'УКРАЇНСЬКА';
    }

    public function language()
    {
        return new LanguageUk();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }
}
