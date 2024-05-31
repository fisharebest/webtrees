<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageHr;

/**
 * Class LocaleHr - Croatian
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleHr extends AbstractLocale implements LocaleInterface
{
    public function collation()
    {
        return 'croatian_ci';
    }

    public function endonym()
    {
        return 'hrvatski';
    }

    public function endonymSortable()
    {
        return 'HRVATSKI';
    }

    public function language()
    {
        return new LanguageHr();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP    => self::DOT,
            self::DECIMAL  => self::COMMA,
            self::NEGATIVE => self::MINUS_SIGN,
        );
    }

    /**
     * How to format a floating point number (%s) as a percentage.
     *
     * @return string
     */
    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::NBSP . self::PERCENT;
    }
}
