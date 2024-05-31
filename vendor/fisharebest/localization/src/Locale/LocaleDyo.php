<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageDyo;

/**
 * Class LocaleDyo - Jola-Fonyi
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleDyo extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'joola';
    }

    public function endonymSortable()
    {
        return 'JOOLA';
    }

    public function language()
    {
        return new LanguageDyo();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }
}
