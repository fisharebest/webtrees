<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptJamo - Representation of the Jamo script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptJamo extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Jamo';
    }

    public function number()
    {
        return '284';
    }
}
