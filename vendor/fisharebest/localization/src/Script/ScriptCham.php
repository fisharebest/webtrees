<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptCham - Representation of the Cham script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptCham extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Cham';
    }

    public function numerals()
    {
        return array('꩐', '꩑', '꩒', '꩓', '꩔', '꩕', '꩖', '꩗', '꩘', '꩙');
    }

    public function number()
    {
        return '358';
    }

    public function unicodeName()
    {
        return 'Cham';
    }
}
