<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKk;

/**
 * Class LocaleKk - Kazakh
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleKk extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'қазақ тілі';
    }

    public function endonymSortable()
    {
        return 'ҚАЗАҚ ТІЛІ';
    }

    public function language()
    {
        return new LanguageKk();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }
}
