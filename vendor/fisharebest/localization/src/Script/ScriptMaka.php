<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptCpmn - Representation of the Makasar script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptMaka extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Maka';
    }

    public function number()
    {
        return '366';
    }

    public function unicodeName()
    {
        return 'Makasar';
    }
}
