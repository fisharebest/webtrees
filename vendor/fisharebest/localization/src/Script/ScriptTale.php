<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptTale - Representation of the Tai Le script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptTale extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Tale';
    }

    public function number()
    {
        return '353';
    }

    public function unicodeName()
    {
        return 'Tai_Le';
    }
}
