<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory LR - Liberia.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryLr extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'LR';
    }

    public function measurementSystem()
    {
        return 'US';
    }
}
