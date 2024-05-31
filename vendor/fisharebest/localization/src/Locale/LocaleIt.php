<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageIt;

/**
 * Class LocaleIt - Italian
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleIt extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'italiano';
    }

    public function endonymSortable()
    {
        return 'ITALIANO';
    }

    public function language()
    {
        return new LanguageIt();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }
}
