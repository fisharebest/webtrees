<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptThaa - Representation of the Thaana script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptThaa extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Thaa';
    }

    public function number()
    {
        return '170';
    }

    public function unicodeName()
    {
        return 'Thaana';
    }
}
