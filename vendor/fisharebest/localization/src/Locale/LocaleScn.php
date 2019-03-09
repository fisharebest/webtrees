<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageScn;

/**
 * Class LocaleScn - Sicilain
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocaleScn extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Sicilianu';
    }

    public function endonymSortable()
    {
        return 'SICILIANU';
    }

    public function language()
    {
        return new LanguageScn();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }
}
