<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory GT - Guatemala.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
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
