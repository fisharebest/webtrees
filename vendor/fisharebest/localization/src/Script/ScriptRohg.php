<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptRunr - Representation of the Hanifi Rohingya script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class ScriptRohg extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Rohg';
    }

    public function number()
    {
        return '167';
    }

    public function unicodeName()
    {
        return 'Hanifi_Rohingya';
    }
}
