<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptShrd - Representation of the Sharada, Śāradā script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptShrd extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Shrd';
    }

    public function numerals()
    {
        return array('𑇐', '𑇑', '𑇒', '𑇓', '𑇔', '𑇕', '𑇖', '𑇗', '𑇘', '𑇙');
    }

    public function number()
    {
        return '319';
    }

    public function unicodeName()
    {
        return 'Sharada';
    }
}
