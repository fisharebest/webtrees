<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNd;

/**
 * Class LocaleNd - North Ndebele
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleNd extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'isiNdebele';
    }

    public function endonymSortable()
    {
        return 'ISINDEBELE';
    }

    public function language()
    {
        return new LanguageNd();
    }
}
