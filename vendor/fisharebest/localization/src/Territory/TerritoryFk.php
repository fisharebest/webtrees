<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory FK - Falkland Islands (Malvinas).
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryFk extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'FK';
    }
}
