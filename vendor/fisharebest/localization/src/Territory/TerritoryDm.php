<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory DM - Dominica.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class TerritoryDm extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'DM';
    }

    public function firstDay()
    {
        return 0;
    }
}
