<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory NZ - New Zealand.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class TerritoryNz extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'NZ';
    }
}
