<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSora - Representation of the Sora Sompeng script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptSora extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Sora';
    }

    public function numerals()
    {
        return array('𑃰', '𑃱', '𑃲', '𑃳', '𑃴', '𑃵', '𑃶', '𑃷', '𑃸', '𑃹');
    }

    public function number()
    {
        return '398';
    }

    public function unicodeName()
    {
        return 'Sora_Sompeng';
    }
}
