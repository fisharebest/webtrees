<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageAsa;

/**
 * Class LocaleAsa - Asu
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleAsa extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Kipare';
    }

    public function endonymSortable()
    {
        return 'KIPARE';
    }

    public function language()
    {
        return new LanguageAsa();
    }
}
