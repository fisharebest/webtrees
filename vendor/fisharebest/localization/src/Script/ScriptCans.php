<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptCans - Representation of the Unified Canadian Aboriginal Syllabics script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptCans extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Cans';
    }

    public function number()
    {
        return '440';
    }

    public function unicodeName()
    {
        return 'Canadian_Aboriginal';
    }
}
