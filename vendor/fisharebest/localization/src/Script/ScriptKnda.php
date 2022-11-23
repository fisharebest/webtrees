<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptKnda - Representation of the Kannada script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptKnda extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Knda';
    }

    public function numerals()
    {
        return array('೦', '೧', '೨', '೩', '೪', '೫', '೬', '೭', '೮', '೯');
    }

    public function number()
    {
        return '345';
    }

    public function unicodeName()
    {
        return 'Kannada';
    }
}
