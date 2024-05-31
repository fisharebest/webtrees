<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageEn;

/**
 * Class LocaleEn - English
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEn extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'English';
    }

    public function endonymSortable()
    {
        return 'ENGLISH';
    }

    public function language()
    {
        return new LanguageEn();
    }
}
