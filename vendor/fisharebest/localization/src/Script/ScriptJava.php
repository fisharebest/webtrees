<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptJava - Representation of the Javanese script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptJava extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Java';
    }

    public function numerals()
    {
        return array('꧐', '꧑', '꧒', '꧓', '꧔', '꧕', '꧖', '꧗', '꧘', '꧙');
    }

    public function number()
    {
        return '361';
    }

    public function unicodeName()
    {
        return 'Javanese';
    }
}
