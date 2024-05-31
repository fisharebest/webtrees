<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory SG - Singapore.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritorySg extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'SG';
    }

    public function firstDay()
    {
        return 0;
    }
}
