<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory BV - Bouvet Island.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryBv extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'BV';
    }
}
