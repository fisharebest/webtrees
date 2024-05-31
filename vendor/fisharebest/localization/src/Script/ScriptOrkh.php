<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptOrkh - Representation of the Old Turkic, Orkhon Runic script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptOrkh extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Orkh';
    }

    public function number()
    {
        return '175';
    }

    public function unicodeName()
    {
        return 'Old_Turkic';
    }
}
