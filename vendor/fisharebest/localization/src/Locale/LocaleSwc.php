<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSwc;

/**
 * Class LocaleSwc - Congo Swahili
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSwc extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Kiswahili ya Kongo';
    }

    public function endonymSortable()
    {
        return 'KISWAHILI YA KONGO';
    }

    public function language()
    {
        return new LanguageSwc();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }
}
