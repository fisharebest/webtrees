<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory EZ - Eurozone.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryEz extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'EZ';
    }
}
