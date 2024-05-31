<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptAhom - Representation of the Ahom script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptAhom extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Ahom';
    }

    public function number()
    {
        return '338';
    }

    public function unicodeName()
    {
        return 'Ahom';
    }
}
