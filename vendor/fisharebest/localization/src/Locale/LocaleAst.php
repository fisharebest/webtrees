<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageAst;

/**
 * Class LocaleAst - Asturian
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleAst extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'asturianu';
    }

    public function endonymSortable()
    {
        return 'ASTURIANU';
    }

    public function language()
    {
        return new LanguageAst();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }
}
