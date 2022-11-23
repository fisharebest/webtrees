<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageFo;

/**
 * Class LocaleFo - Faroese
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleFo extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'føroyskt';
    }

    public function endonymSortable()
    {
        return 'FOROYSKT';
    }

    public function language()
    {
        return new LanguageFo();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP    => self::DOT,
            self::DECIMAL  => self::COMMA,
            self::NEGATIVE => self::MINUS_SIGN,
        );
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::NBSP . self::PERCENT;
    }
}
