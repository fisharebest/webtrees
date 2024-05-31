<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptLydi - Representation of the Lydian script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptLydi extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Lydi';
    }

    public function number()
    {
        return '116';
    }

    public function unicodeName()
    {
        return 'Lydian';
    }
}
