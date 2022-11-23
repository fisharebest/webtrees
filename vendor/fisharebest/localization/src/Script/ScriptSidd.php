<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSidd - Representation of the Siddham, Siddhaṃ, Siddhamātṛkā script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptSidd extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Sidd';
    }

    public function number()
    {
        return '302';
    }

    public function unicodeName()
    {
        return 'Siddham';
    }
}
