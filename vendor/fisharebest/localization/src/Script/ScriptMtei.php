<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMtei - Representation of the Meitei Mayek (Meithei, Meetei) script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptMtei extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Mtei';
    }

    public function numerals()
    {
        return array('꯰', '꯱', '꯲', '꯳', '꯴', '꯵', '꯶', '꯷', '꯸', '꯹');
    }

    public function number()
    {
        return '337';
    }

    public function unicodeName()
    {
        return 'Meetei_Mayek';
    }
}
