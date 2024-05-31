<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKaj;

/**
 * Class LocaleKaj - Jju
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleKaj extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Jju';
    }

    public function endonymSortable()
    {
        return 'JJU';
    }

    public function language()
    {
        return new LanguageKaj();
    }
}
