<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory BH - Bahrain.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryBh extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'BH';
    }

    public function firstDay()
    {
        return 6;
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
