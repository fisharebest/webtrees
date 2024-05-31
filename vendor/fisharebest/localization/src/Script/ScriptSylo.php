<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSylo - Representation of the Syloti Nagri script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptSylo extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Sylo';
    }

    public function number()
    {
        return '316';
    }

    public function unicodeName()
    {
        return 'Syloti_Nagri';
    }
}
