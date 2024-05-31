<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptPerm - Representation of the Old Permic script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptPerm extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Perm';
    }

    public function number()
    {
        return '227';
    }

    public function unicodeName()
    {
        return 'Old_Permic';
    }
}
