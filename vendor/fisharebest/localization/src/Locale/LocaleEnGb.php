<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGb;

/**
 * Class LocaleEnGb - British English
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEnGb extends LocaleEn
{
    public function endonym()
    {
        return 'British English';
    }

    public function endonymSortable()
    {
        return 'ENGLISH, BRITISH';
    }

    public function territory()
    {
        return new TerritoryGb();
    }
}
