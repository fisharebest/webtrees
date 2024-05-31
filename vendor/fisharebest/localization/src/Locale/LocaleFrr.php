<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageFrr;

/**
 * Class LocaleEn - English
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleFrr extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Nordfriisk';
    }

    public function endonymSortable()
    {
        return 'NORDFRIISK';
    }

    public function language()
    {
        return new LanguageFrr();
    }

    public function numberSymbols()
    {
        return array(
            self::DECIMAL => self::COMMA,
            self::GROUP   => self::DOT,
        );
    }
}
