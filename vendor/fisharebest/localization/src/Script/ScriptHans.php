<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptHans - Representation of the Simplified Han script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptHans extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Hans';
    }

    public function number()
    {
        return '501';
    }
}
