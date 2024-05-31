<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageRm;

/**
 * Class LocaleRm - Romansh
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleRm extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'rumantsch';
    }

    public function endonymSortable()
    {
        return 'RUMANTSCH';
    }

    public function language()
    {
        return new LanguageRm();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP    => self::APOSTROPHE,
            self::NEGATIVE => self::MINUS_SIGN,
        );
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::NBSP . self::PERCENT;
    }
}
