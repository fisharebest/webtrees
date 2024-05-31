<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTl;

/**
 * Class LocaleTl - Tagalog
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleTl extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Tagalog';
    }

    public function endonymSortable()
    {
        return 'TAGALOG';
    }

    public function language()
    {
        return new LanguageTl();
    }
}
