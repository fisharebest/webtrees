<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptVith - Representation of the Vithkuqi script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptVith extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Vith';
    }

    public function number()
    {
        return '228';
    }

    public function unicodeName()
    {
        return 'Vithkuqi';
    }
}
