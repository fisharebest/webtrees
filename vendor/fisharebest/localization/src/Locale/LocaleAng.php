<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageAng;

/**
 * Class LocaleAng - Anglo-Saxon / Old-English
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleAng extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Ænglisc';
    }

    public function endonymSortable()
    {
        return 'ÆNGLISC';
    }

    public function language()
    {
        return new LanguageAng();
    }
}
