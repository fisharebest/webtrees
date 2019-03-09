<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSl;

/**
 * Class LocaleSl - Slovenian
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocaleSl extends AbstractLocale implements LocaleInterface
{
    public function collation()
    {
        return 'slovenian_ci';
    }

    public function endonym()
    {
        return 'slovenščina';
    }

    public function endonymSortable()
    {
        return 'SLOVENSCINA';
    }

    public function language()
    {
        return new LanguageSl();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP    => self::DOT,
            self::DECIMAL  => self::COMMA,
            self::NEGATIVE => self::MINUS_SIGN,
        );
    }

    public function percentFormat()
    {
        return self::PLACEHOLDER . self::NBSP . self::PERCENT;
    }
}
