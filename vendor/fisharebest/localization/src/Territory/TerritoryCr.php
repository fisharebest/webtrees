<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory CR - Costa Rica.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryCr extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'CR';
    }

    public function paperSize()
    {
        return 'US-Letter';
    }
}
