<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMgh;

/**
 * Class LocaleMgh - Makhuwa-Meetto
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleMgh extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Makua';
    }

    public function endonymSortable()
    {
        return 'MAKUA';
    }

    public function language()
    {
        return new LanguageMgh();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }
}
