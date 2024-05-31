<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSv;

/**
 * Class LocaleSv - Swedish
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSv extends AbstractLocale implements LocaleInterface
{
    public function collation()
    {
        return 'swedish_ci';
    }

    public function endonym()
    {
        return 'svenska';
    }

    public function endonymSortable()
    {
        return 'SVENSKA';
    }

    public function language()
    {
        return new LanguageSv();
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
