<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory MT - Malta.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryMt extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'MT';
    }

    public function firstDay()
    {
        return 0;
    }
}
