<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptTfng - Representation of the Tifinagh script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptTfng extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Tfng';
    }

    public function number()
    {
        return '120';
    }

    public function unicodeName()
    {
        return 'Tifinagh';
    }
}
