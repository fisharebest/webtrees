<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageFa;

/**
 * Class LocaleFa - Persian
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleFa extends AbstractLocale implements LocaleInterface
{
    public function collation()
    {
        return 'persian_ci';
    }

    public function endonym()
    {
        return 'فارسی';
    }

    public function language()
    {
        return new LanguageFa();
    }

    public function numerals()
    {
        return array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP    => self::ARAB_GROUP,
            self::DECIMAL  => self::ARAB_DECIMAL,
            self::NEGATIVE => self::LTR_MARK . self::MINUS_SIGN,
        );
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::ARAB_PERCENT;
    }
}
