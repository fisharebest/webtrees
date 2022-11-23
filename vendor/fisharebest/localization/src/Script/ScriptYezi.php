<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptYezi - Representation of the Yezidi script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptYezi extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Yezi';
    }

    public function number()
    {
        return '192';
    }

    public function unicodeName()
    {
        return 'Yezidi';
    }
}
