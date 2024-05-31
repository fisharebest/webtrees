<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory NG - Nigeria.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryNg extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'NG';
    }

    public function firstDay()
    {
        return 0;
    }
}
