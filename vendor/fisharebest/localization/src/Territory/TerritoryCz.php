<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory CZ - Czech Republic.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryCz extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'CZ';
    }
}
