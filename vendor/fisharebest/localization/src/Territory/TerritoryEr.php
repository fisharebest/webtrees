<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory ER - Eritrea.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryEr extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'ER';
    }

    public function firstDay()
    {
        return 6;
    }
}
