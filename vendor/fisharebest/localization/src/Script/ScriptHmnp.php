<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptCpmn - Representation of the Nyiakeng Puachue Hmong script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptHmnp extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Hmnp';
    }

    public function number()
    {
        return '451';
    }

    public function unicodeName()
    {
        return 'Nyiakeng_Puachue_Hmong';
    }
}
