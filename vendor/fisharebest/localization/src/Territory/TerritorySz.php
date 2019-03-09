<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory SZ - Swaziland.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class TerritorySz extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'SZ';
    }
}
