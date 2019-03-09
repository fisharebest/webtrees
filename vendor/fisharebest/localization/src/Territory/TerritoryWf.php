<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory WF - Wallis and Futuna.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class TerritoryWf extends AbstractTerritory implements TerritoryInterface
{
    public function code()
    {
        return 'WF';
    }
}
