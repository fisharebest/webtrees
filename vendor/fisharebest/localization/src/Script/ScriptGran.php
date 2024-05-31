<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptGran - Representation of the Grantha script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptGran extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Gran';
    }

    public function number()
    {
        return '343';
    }

    public function unicodeName()
    {
        return 'Grantha';
    }
}
