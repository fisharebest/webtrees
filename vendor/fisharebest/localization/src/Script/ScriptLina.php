<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptLina - Representation of the Linear A script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptLina extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Lina';
    }

    public function number()
    {
        return '400';
    }

    public function unicodeName()
    {
        return 'Linear_A';
    }
}
