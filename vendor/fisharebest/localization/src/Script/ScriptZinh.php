<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptZinh - Representation of the Code for inherited script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptZinh extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Zinh';
    }

    public function number()
    {
        return '994';
    }

    public function unicodeName()
    {
        return 'Inherited';
    }
}
