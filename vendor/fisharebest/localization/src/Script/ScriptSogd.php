<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSogd - Representation of the Sogdian script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class ScriptSogd extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Sogd';
    }

    public function number()
    {
        return '141';
    }

    public function unicodeName()
    {
        return 'Sogdian';
    }
}
