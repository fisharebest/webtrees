<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageFur;

/**
 * Class LocaleFur - Friulian
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleFur extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'furlan';
    }

    public function endonymSortable()
    {
        return 'FURLAN';
    }

    public function language()
    {
        return new LanguageFur();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }
}
