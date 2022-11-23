<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory VI - U.S. Virgin Islands.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryVi extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'VI';
    }

    public function firstDay()
    {
        return 0;
    }
}
