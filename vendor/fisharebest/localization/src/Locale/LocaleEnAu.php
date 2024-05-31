<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryAu;

/**
 * Class LocaleEnAu - Australian English
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEnAu extends LocaleEn
{
    public function endonym()
    {
        return 'Australian English';
    }

    public function endonymSortable()
    {
        return 'ENGLISH, AUSTRALIAN';
    }

    public function territory()
    {
        return new TerritoryAu();
    }
}
