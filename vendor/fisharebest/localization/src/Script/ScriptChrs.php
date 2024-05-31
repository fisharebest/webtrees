<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptChrs - Representation of the Chorasmian script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptChrs extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Chrs';
    }

    public function number()
    {
        return '109';
    }

    public function unicodeName()
    {
        return 'Chorasmian';
    }
}
