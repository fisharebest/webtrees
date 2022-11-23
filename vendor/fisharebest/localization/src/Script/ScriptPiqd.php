<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptPiqd - Representation of the Piqd script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptPiqd extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Piqd';
    }

    public function number()
    {
        return '293';
    }
}
