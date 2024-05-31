<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTzm;

/**
 * Class LocaleTzm - Central Atlas Tamazight
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleTzm extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Tamaziɣt n laṭlaṣ';
    }

    public function endonymSortable()
    {
        return 'TAMAZIGHT N LATLAS';
    }

    public function language()
    {
        return new LanguageTzm();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }
}
