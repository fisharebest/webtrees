<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptLepc - Representation of the Lepcha (Róng) script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptLepc extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Lepc';
    }

    public function numerals()
    {
        return array('᱀', '᱁', '᱂', '᱃', '᱄', '᱅', '᱆', '᱇', '᱈', '᱉');
    }

    public function number()
    {
        return '335';
    }

    public function unicodeName()
    {
        return 'Lepcha';
    }
}
