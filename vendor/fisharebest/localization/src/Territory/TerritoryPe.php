<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory PE - Peru.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryPe extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'PE';
    }

    public function firstDay()
    {
        return 0;
    }
}
