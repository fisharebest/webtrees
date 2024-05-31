<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptNand - Representation of the Nandinagari script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptNand extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Nand';
    }

    public function number()
    {
        return '311';
    }

    public function unicodeName()
    {
        return 'Nandinagari';
    }
}
