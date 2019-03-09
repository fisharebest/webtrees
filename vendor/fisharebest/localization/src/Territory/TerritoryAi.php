<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory AI - Anguilla.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class TerritoryAi extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'AI';
    }
}
