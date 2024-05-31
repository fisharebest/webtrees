<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory CX - Christmas Island.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryCx extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'CX';
    }
}
