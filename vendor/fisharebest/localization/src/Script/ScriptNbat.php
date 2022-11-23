<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptNbat - Representation of the Nabataean script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptNbat extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Nbat';
    }

    public function number()
    {
        return '159';
    }

    public function unicodeName()
    {
        return 'Nabataean';
    }
}
