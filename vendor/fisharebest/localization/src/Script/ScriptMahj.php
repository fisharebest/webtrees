<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMahj - Representation of the Mahajani script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptMahj extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Mahj';
    }

    public function number()
    {
        return '314';
    }

    public function unicodeName()
    {
        return 'Mahajani';
    }
}
