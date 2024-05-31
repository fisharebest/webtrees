<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory KY - Cayman Islands.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryKy extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'KY';
    }

    public function measurementSystem()
    {
        return 'US';
    }
}
