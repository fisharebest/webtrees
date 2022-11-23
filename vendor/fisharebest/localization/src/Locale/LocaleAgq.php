<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageAgq;

/**
 * Class LocaleAgq - Aghem
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleAgq extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Aghem';
    }

    public function endonymSortable()
    {
        return 'AGHEM';
    }

    public function language()
    {
        return new LanguageAgq();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }
}
