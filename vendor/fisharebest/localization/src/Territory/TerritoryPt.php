<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory PT - Portugal.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryPt extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'PT';
    }

    /**
     * @return int
     */
    public function firstDay()
    {
        return 0;
    }
}
