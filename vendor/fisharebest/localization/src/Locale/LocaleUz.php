<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageUz;

/**
 * Class LocaleUz - Uzbek
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleUz extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'o‘zbek';
    }

    public function endonymSortable()
    {
        return 'OZBEK';
    }

    public function language()
    {
        return new LanguageUz();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }
}
