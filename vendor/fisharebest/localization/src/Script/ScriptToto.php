<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptToto - Representation of the Toto script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptToto extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Toto';
    }

    public function number()
    {
        return '294';
    }

    public function unicodeName()
    {
        return 'Toto';
    }
}
