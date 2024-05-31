<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptBeng - Representation of the Bengali script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptBeng extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Beng';
    }

    public function numerals()
    {
        return array('০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯');
    }

    public function number()
    {
        return '325';
    }

    public function unicodeName()
    {
        return 'Bengali';
    }
}
