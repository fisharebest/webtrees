<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageRof;

/**
 * Class LocaleRof - Rombo
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleRof extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Kihorombo';
    }

    public function endonymSortable()
    {
        return 'KIHOROMBO';
    }

    public function language()
    {
        return new LanguageRof();
    }
}
