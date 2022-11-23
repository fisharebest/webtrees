<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptOugr - Representation of the Old Uyghur script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptOugr extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Ougr';
    }

    public function number()
    {
        return '143';
    }

    public function unicodeName()
    {
        return 'Old_Uyghur';
    }
}
