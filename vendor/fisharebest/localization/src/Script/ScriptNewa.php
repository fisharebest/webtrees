<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptNewa - Representation of the Newa script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class ScriptNewa extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Newa';
    }

    public function number()
    {
        return '333';
    }

    public function unicodeName()
    {
        return 'Newa';
    }
}
