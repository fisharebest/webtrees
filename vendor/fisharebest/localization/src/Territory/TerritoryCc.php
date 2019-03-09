<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory CC - Cocos (Keeling) Islands.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class TerritoryCc extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'CC';
    }
}
