<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptVisp - Representation of the Visible Speech script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptVisp extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Visp';
    }

    public function number()
    {
        return '280';
    }
}
