<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSyrc - Representation of the Old Sogdian script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptSogo extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Sogo';
    }

    public function number()
    {
        return '142';
    }

    public function unicodeName()
    {
        return 'Old_Sogdian';
    }
}
