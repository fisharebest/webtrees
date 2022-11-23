<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory IO - British Indian Ocean AbstractTerritory.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class TerritoryIo extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'IO';
    }
}
