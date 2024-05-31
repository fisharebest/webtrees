<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguagePis;

/**
 * Class LocaleEn - English
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocalePis extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Pijin';
    }

    public function endonymSortable()
    {
        return 'Pijin';
    }

    public function language()
    {
        return new LanguagePis();
    }
}
