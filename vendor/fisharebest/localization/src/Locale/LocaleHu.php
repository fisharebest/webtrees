<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageHu;

/**
 * Class LocaleHu - Hungarian
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleHu extends AbstractLocale implements LocaleInterface
{
    public function collation()
    {
        return 'hungarian_ci';
    }

    public function endonym()
    {
        return 'magyar';
    }

    public function endonymSortable()
    {
        return 'MAGYAR';
    }

    public function language()
    {
        return new LanguageHu();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }
}
