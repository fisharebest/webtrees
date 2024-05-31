<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory TT - Trinidad and Tobago.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryTt extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'TT';
    }

    public function firstDay()
    {
        return 0;
    }
}
