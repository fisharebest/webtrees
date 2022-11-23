<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptKawi - Representation of the Kawi script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptKawi extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Kawi';
    }

    public function number()
    {
        return '368';
    }
}
