<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMani - Representation of the Manichaean script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptMani extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Mani';
    }

    public function number()
    {
        return '139';
    }

    public function unicodeName()
    {
        return 'Manichaean';
    }
}
