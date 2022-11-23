<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageFi;

/**
 * Class LocaleFi - Finnish
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleFi extends AbstractLocale implements LocaleInterface
{
    public function collation()
    {
        return 'swedish_ci';
    }

    public function endonym()
    {
        return 'suomi';
    }

    public function endonymSortable()
    {
        return 'SUOMI';
    }

    public function language()
    {
        return new LanguageFi();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP    => self::NBSP,
            self::DECIMAL  => self::COMMA,
            self::NEGATIVE => self::MINUS_SIGN,
        );
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::NBSP . self::PERCENT;
    }
}
