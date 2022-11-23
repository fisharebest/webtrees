<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptJpan - Representation of the Japanese script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptJpan extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Jpan';
    }

    public function number()
    {
        return '413';
    }
}
