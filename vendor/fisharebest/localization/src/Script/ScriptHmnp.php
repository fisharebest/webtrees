<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptCpmn - Representation of the Nyiakeng Puachue Hmong script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
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
