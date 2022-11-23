<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory HM - Heard Island and McDonald Islands.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryHm extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'HM';
    }
}
