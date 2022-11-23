<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory UG - Uganda.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryUg extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'UG';
    }

    /**
     * @return int
     */
    public function weekendStart()
    {
        return 0;
    }

    /**
     * @return int
     */
    public function weekendEnd()
    {
        return 1;
    }
}
