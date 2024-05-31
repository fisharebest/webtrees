<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory MV - Maldives.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryMv extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'MV';
    }

    public function firstDay()
    {
        return 5;
    }
}
