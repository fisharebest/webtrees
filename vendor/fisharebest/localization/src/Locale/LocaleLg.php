<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLg;

/**
 * Class LocaleLg - Ganda
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleLg extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Luganda';
    }

    public function endonymSortable()
    {
        return 'LUGANDA';
    }

    public function language()
    {
        return new LanguageLg();
    }
}
