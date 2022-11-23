<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptWara - Representation of the Wancho script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptWcho extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Wcho';
    }

    public function number()
    {
        return '283';
    }

    public function unicodeName()
    {
        return 'Wancho';
    }
}
