<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory JP - Japan.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryJp extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'JP';
    }

    public function firstDay()
    {
        return 0;
    }
}
