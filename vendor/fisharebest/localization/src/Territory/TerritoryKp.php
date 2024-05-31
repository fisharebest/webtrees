<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory KP - Democratic People's Republic of Korea.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryKp extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'KP';
    }
}
