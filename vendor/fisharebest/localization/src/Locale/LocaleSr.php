<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSr;

/**
 * Class LocaleSr - Serbian
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSr extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'српски';
    }

    public function endonymSortable()
    {
        return 'СРПСКИ';
    }

    public function language()
    {
        return new LanguageSr();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }
}
