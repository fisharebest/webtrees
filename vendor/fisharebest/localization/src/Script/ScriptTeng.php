<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptTeng - Representation of the Tengwar script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptTeng extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Teng';
    }

    public function number()
    {
        return '290';
    }
}
