<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptWara - Representation of the Zanabazar Square script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptZanb extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Zanb';
    }

    public function number()
    {
        return '339';
    }

    public function unicodeName()
    {
        return 'Zanabazar_Square';
    }
}
