<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptTelu - Representation of the Telugu script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptTelu extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Telu';
    }

    public function numerals()
    {
        return array('౦', '౧', '౨', '౩', '౪', '౫', '౬', '౭', '౮', '౯');
    }

    public function number()
    {
        return '340';
    }

    public function unicodeName()
    {
        return 'Telugu';
    }
}
