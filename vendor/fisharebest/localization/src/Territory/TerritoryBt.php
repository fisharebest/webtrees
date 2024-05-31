<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory BT - Bhutan.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryBt extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'BT';
    }

    public function firstDay()
    {
        return 0;
    }
}
