<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory AF - Afghanistan.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryAf extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'AF';
    }

    public function firstDay()
    {
        return 6;
    }

    public function weekendStart()
    {
        return 4;
    }

    public function weekendEnd()
    {
        return 5;
    }
}
