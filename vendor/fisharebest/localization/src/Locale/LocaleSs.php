<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSs;

/**
 * Class LocaleSs - Swati
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSs extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Siswati';
    }

    public function endonymSortable()
    {
        return 'SISWATI';
    }

    public function language()
    {
        return new LanguageSs();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }
}
