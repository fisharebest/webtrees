<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptTakr - Representation of the Takri, Ṭākrī, Ṭāṅkrī script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptTakr extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Takr';
    }

    public function numerals()
    {
        return array('𑛀', '𑛁', '𑛂', '𑛃', '𑛄', '𑛅', '𑛆', '𑛇', '𑛈', '𑛉');
    }

    public function number()
    {
        return '321';
    }

    public function unicodeName()
    {
        return 'Takri';
    }
}
