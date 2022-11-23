<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNo;

/**
 * Class LocaleNo - Norwegian Nynorsk
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleNo extends AbstractLocale implements LocaleInterface
{
    public function collation()
    {
        return 'danish_ci';
    }

    public function endonym()
    {
        return 'norsk';
    }

    public function endonymSortable()
    {
        return 'NORSK';
    }

    public function language()
    {
        return new LanguageNo();
    }

    public function numberSymbols()
    {
        return array(
            self::DECIMAL  => self::COMMA,
            self::GROUP    => self::NBSP,
            self::NEGATIVE => self::MINUS_SIGN,
        );
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::NBSP . self::PERCENT;
    }
}
