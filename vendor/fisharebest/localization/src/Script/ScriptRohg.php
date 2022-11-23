<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptRunr - Representation of the Hanifi Rohingya script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptRohg extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Rohg';
    }

    public function number()
    {
        return '167';
    }

    public function unicodeName()
    {
        return 'Hanifi_Rohingya';
    }
}
