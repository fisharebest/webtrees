<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptKits - Representation of the Khitan small script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptKits extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Kits';
    }

    public function number()
    {
        return '288';
    }

    public function unicodeName()
    {
        return 'Khitan_Small_Script';
    }
}
