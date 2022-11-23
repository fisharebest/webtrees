<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptCari - Representation of the Carian script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptCari extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Cari';
    }

    public function number()
    {
        return '201';
    }

    public function unicodeName()
    {
        return 'Carian';
    }
}
