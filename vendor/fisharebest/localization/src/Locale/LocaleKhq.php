<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKhq;

/**
 * Class LocaleKhq - Koyra Chiini
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleKhq extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Koyra ciini';
    }

    public function endonymSortable()
    {
        return 'KOYRA CIINI';
    }

    public function language()
    {
        return new LanguageKhq();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP => self::NBSP,
        );
    }
}
