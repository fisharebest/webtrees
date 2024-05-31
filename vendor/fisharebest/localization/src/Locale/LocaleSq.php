<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSq;

/**
 * Class LocaleSq - Albanian
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSq extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'shqip';
    }

    public function endonymSortable()
    {
        return 'SHQIP';
    }

    public function language()
    {
        return new LanguageSq();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }
}
