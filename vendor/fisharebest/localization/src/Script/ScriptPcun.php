<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptPcun - Representation of the Proto Cuneiform script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptPcun extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Pcun';
    }

    public function number()
    {
        return '015';
    }
}
