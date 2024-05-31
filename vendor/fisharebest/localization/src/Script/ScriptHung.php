<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptHung - Representation of the Old Hungarian (Hungarian Runic) script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptHung extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Hung';
    }

    public function number()
    {
        return '176';
    }

    public function unicodeName()
    {
        return 'Old_Hungarian';
    }
}
