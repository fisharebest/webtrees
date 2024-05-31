<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMua;

/**
 * Class LocaleMua - Mundang
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleMua extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'MUNDAŊ';
    }

    public function endonymSortable()
    {
        return 'MUNDAN';
    }

    public function language()
    {
        return new LanguageMua();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }
}
