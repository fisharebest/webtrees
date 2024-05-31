<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptTalu - Representation of the New Tai Lue script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptTalu extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Talu';
    }

    public function numerals()
    {
        return array('᧐', '᧑', '᧒', '᧓', '᧔', '᧕', '᧖', '᧗', '᧘', '᧙');
    }

    public function number()
    {
        return '354';
    }

    public function unicodeName()
    {
        return 'New_Tai_Lue';
    }
}
