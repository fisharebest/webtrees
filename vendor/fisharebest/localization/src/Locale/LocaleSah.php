<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSah;

/**
 * Class LocaleSah - Sakha
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSah extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'саха тыла';
    }

    public function endonymSortable()
    {
        return 'САХА ТЫЛА';
    }

    public function language()
    {
        return new LanguageSah();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }
}
