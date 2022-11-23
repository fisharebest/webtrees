<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSw;

/**
 * Class LocaleSw - Swahili
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSw extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Kiswahili';
    }

    public function endonymSortable()
    {
        return 'KISWAHILI';
    }

    public function language()
    {
        return new LanguageSw();
    }
}
