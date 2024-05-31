<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory PR - Puerto Rico.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryPr extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'PR';
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
