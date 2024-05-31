<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptArmn - Representation of the Armenian script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptArmn extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Armn';
    }

    public function number()
    {
        return '230';
    }

    public function unicodeName()
    {
        return 'Armenian';
    }
}
