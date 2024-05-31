<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptHmng - Representation of the Pahawh Hmong script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptHmng extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Hmng';
    }

    public function number()
    {
        return '450';
    }

    public function unicodeName()
    {
        return 'Pahawh_Hmong';
    }
}
