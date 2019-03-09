<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSmn;

/**
 * Class LocaleSmn - Inari Sami
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocaleSmn extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'anarâškielâ';
    }

    public function endonymSortable()
    {
        return 'ANARASKIELA';
    }

    public function language()
    {
        return new LanguageSmn();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }

    public function percentFormat()
    {
        return self::PLACEHOLDER . self:: NBSP . self::PERCENT;
    }
}
