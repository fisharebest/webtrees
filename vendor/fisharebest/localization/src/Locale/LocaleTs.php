<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTs;

/**
 * Class LocaleTs - Tsonga
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleTs extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Xitsonga';
    }

    public function endonymSortable()
    {
        return 'XITSONGA';
    }

    public function language()
    {
        return new LanguageTs();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }
}
