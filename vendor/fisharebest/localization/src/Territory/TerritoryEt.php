<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory ET - Ethiopia.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryEt extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'ET';
    }

    public function firstDay()
    {
        return 0;
    }
}
