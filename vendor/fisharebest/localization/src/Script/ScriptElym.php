<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptCpmn - Representation of the Elymaic script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class ScriptElym extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Elym';
    }

    public function number()
    {
        return '128';
    }

    public function unicodeName()
    {
        return 'Elymaic';
    }
}
