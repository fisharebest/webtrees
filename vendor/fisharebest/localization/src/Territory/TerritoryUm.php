<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory UM - United States Minor Outlying Islands.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryUm extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'UM';
    }

    public function firstDay()
    {
        return 0;
    }
}
