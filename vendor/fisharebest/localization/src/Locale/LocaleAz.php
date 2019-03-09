<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageAz;

/**
 * Class LocaleAz - Azerbaijani
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocaleAz extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'azərbaycan';
    }

    public function endonymSortable()
    {
        return 'AZERBAYCAN';
    }

    public function language()
    {
        return new LanguageAz();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }
}
