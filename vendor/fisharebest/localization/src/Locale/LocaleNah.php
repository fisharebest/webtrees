<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNah;

/**
 * Class LocaleNah - Nahuatl
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleNah extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Nahuatlahtolli';
    }

    public function endonymSortable()
    {
        return 'NAHUATLAHTOLLI';
    }

    public function language()
    {
        return new LanguageNah();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::COMMA,
            self::DECIMAL => self::DOT,
        );
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::PERCENT;
    }
}
