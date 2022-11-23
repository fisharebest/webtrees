<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory CH - Switzerland.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryCh extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'CH';
    }
}
