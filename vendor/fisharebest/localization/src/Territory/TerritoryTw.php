<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory TW - Taiwan, Province of China.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryTw extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'TW';
    }

    public function firstDay()
    {
        return 0;
    }
}
