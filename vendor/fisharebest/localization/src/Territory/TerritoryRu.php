<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory RU - Russian Federation.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class TerritoryRu extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'RU';
    }
}
