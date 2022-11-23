<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory RU - Russian Federation.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryRu extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'RU';
    }
}
