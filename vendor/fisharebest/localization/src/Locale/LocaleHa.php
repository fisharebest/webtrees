<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageHa;

/**
 * Class LocaleHa - Hausa
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleHa extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Hausa';
    }

    public function endonymSortable()
    {
        return 'HAUSA';
    }

    public function language()
    {
        return new LanguageHa();
    }
}
