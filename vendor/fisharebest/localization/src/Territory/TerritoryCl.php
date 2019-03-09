<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory CL - Chile.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class TerritoryCl extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'CL';
    }

    public function paperSize()
    {
        return 'US-Letter';
    }
}
