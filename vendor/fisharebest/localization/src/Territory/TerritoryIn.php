<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory IN - India.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryIn extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'IN';
    }

    public function firstDay()
    {
        return 0;
    }

    public function weekendStart()
    {
        return 0;
    }

    public function weekendEnd()
    {
        return 1;
    }
}
