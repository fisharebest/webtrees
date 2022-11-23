<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageEbu;

/**
 * Class LocaleEbu - Embu
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEbu extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Kĩembu';
    }

    public function endonymSortable()
    {
        return 'KIEMBU';
    }

    public function language()
    {
        return new LanguageEbu();
    }
}
