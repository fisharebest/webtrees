<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptAghb - Representation of the Caucasian Albanian script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptAghb extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Aghb';
    }

    public function number()
    {
        return '239';
    }

    public function unicodeName()
    {
        return 'Caucasian_Albanian';
    }
}
