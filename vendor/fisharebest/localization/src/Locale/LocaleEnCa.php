<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCa;

/**
 * Class LocaleEnCa - Canadian English
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEnCa extends LocaleEn
{
    public function endonym()
    {
        return 'Canadian English';
    }

    public function endonymSortable()
    {
        return 'ENGLISH, CANADIAN';
    }

    public function territory()
    {
        return new TerritoryCa();
    }
}
