<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptLoma - Representation of the Loma script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptLoma extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Loma';
    }

    public function number()
    {
        return '437';
    }
}
