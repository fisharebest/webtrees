<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSat;

/**
 * Class LocaleSat - Santali
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSat extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'ᱥᱟᱱᱛᱟᱲᱤ';
    }

    public function endonymSortable()
    {
        return 'ᱥᱟᱱᱛᱟᱲᱤ';
    }

    public function language()
    {
        return new LanguageSat();
    }
}
