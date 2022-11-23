<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTo;

/**
 * Class LocaleTo - Tongan
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleTo extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'lea fakatonga';
    }

    public function endonymSortable()
    {
        return 'LEA FAKATONGA';
    }

    public function language()
    {
        return new LanguageTo();
    }
}
