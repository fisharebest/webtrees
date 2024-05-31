<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptRunr - Representation of the Runic script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptRunr extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Runr';
    }

    public function number()
    {
        return '211';
    }

    public function unicodeName()
    {
        return 'Runic';
    }
}
