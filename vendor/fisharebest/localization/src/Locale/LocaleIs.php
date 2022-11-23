<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageIs;

/**
 * Class LocaleIs - Icelandic
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleIs extends AbstractLocale implements LocaleInterface
{
    public function collation()
    {
        return 'icelandic_ci';
    }

    public function endonym()
    {
        return 'íslenska';
    }

    public function endonymSortable()
    {
        return 'ISLENSKA';
    }

    public function language()
    {
        return new LanguageIs();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }
}
