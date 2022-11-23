<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageRwk;

/**
 * Class LocaleRwk - Rwa
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleRwk extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Kiruwa';
    }

    public function endonymSortable()
    {
        return 'KIRUWA';
    }

    public function language()
    {
        return new LanguageRwk();
    }
}
