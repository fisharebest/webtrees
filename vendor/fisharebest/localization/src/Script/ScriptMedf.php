<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptCpmn - Representation of the Medefaidrin script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptMedf extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Medf';
    }

    public function number()
    {
        return '265';
    }

    public function unicodeName()
    {
        return 'Medefaidrin';
    }
}
