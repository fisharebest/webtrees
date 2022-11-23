<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptZyyy - Representation of the Code for undetermined script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptZyyy extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Zyyy';
    }

    public function number()
    {
        return '998';
    }

    public function unicodeName()
    {
        return 'Common';
    }
}
