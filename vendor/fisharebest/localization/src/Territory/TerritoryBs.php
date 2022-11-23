<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory BS - Bahamas.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryBs extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'BS';
    }

    public function firstDay()
    {
        return 0;
    }

    public function measurementSystem()
    {
        return 'US';
    }
}
