<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory AE - United Arab Emirates.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryAe extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'AE';
    }

    public function firstDay()
    {
        return 6;
    }

    public function weekendStart()
    {
        return 5;
    }

    public function weekendEnd()
    {
        return 6;
    }
}
