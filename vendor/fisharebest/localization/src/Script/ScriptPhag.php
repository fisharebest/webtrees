<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptPhag - Representation of the Phags-pa script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptPhag extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Phag';
    }

    public function number()
    {
        return '331';
    }

    public function unicodeName()
    {
        return 'Phags_Pa';
    }
}
