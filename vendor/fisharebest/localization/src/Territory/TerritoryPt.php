<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory PT - Portugal.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class TerritoryPt extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'PT';
    }

    /**
     * @return int
     */
    public function firstDay()
    {
        return 0;
    }
}
