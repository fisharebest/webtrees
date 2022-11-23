<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptNkdb - Representation of the Naxi Dongba script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptNkdb extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Nkdb';
    }

    public function number()
    {
        return '085';
    }
}
