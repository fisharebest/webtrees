<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageGl;

/**
 * Class LocaleGl - Galician
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleGl extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'galego';
    }

    public function endonymSortable()
    {
        return 'GALEGO';
    }

    public function language()
    {
        return new LanguageGl();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }

    public function percentFormat()
    {
        return self::PLACEHOLDER . self::NBSP . self::PERCENT;
    }
}
