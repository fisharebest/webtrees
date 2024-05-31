<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptEgyd - Representation of the Egyptian demotic script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptEgyd extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Egyd';
    }

    public function number()
    {
        return '070';
    }
}
