<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory US - United States.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryUs extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'US';
    }

    public function firstDay()
    {
        return 0;
    }

    public function measurementSystem()
    {
        return 'US';
    }

    public function paperSize()
    {
        return 'US-Letter';
    }
}
