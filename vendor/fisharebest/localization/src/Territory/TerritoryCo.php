<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory CO - Colombia.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryCo extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'CO';
    }

    public function firstDay()
    {
        return 0;
    }

    public function paperSize()
    {
        return 'US-Letter';
    }
}
