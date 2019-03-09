<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptTang - Representation of the Tangut script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class ScriptTang extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Tang';
    }

    public function number()
    {
        return '520';
    }

    public function unicodeName()
    {
        return 'Tangut';
    }
}
