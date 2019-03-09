<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptNshu - Representation of the Nüshu script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class ScriptNshu extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Nshu';
    }

    public function number()
    {
        return '499';
    }

    public function unicodeName()
    {
        return 'Nushu';
    }
}
