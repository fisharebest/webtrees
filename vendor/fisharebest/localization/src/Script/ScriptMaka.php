<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptCpmn - Representation of the Makasar script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class ScriptMaka extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Maka';
    }

    public function number()
    {
        return '366';
    }

    public function unicodeName()
    {
        return 'Makasar';
    }
}
