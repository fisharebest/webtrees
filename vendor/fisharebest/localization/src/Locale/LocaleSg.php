<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSg;

/**
 * Class LocaleSg - Sango
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSg extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Sängö';
    }

    public function endonymSortable()
    {
        return 'SANGO';
    }

    public function language()
    {
        return new LanguageSg();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }
}
