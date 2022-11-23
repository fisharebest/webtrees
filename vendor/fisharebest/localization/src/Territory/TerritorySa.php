<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory SA - Saudi Arabia.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritorySa extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'SA';
    }

    public function firstDay()
    {
        return 0;
    }

    public function weekendStart()
    {
        return 5;
    }

    public function weekendEnd()
    {
        return 6;
    }
}
