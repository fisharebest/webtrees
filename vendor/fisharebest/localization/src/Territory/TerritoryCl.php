<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory CL - Chile.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryCl extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'CL';
    }

    public function paperSize()
    {
        return 'US-Letter';
    }
}
