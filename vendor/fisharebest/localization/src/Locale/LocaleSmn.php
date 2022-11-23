<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSmn;

/**
 * Class LocaleSmn - Inari Sami
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSmn extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'anarâškielâ';
    }

    public function endonymSortable()
    {
        return 'ANARASKIELA';
    }

    public function language()
    {
        return new LanguageSmn();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }

    public function percentFormat()
    {
        return self::PLACEHOLDER . self::NBSP . self::PERCENT;
    }
}
