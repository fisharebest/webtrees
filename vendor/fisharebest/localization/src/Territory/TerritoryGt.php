<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory GT - Guatemala.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryGt extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'GT';
    }

    public function firstDay()
    {
        return 0;
    }

    public function paperSize()
    {
        return 'US-Letter';
    }
}
