<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMong - Representation of the Mongolian script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptMong extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Mong';
    }

    public function numerals()
    {
        return array('᠐', '᠑', '᠒', '᠓', '᠔', '᠕', '᠖', '᠗', '᠘', '᠙');
    }

    public function number()
    {
        return '145';
    }

    public function unicodeName()
    {
        return 'Mongolian';
    }
}
