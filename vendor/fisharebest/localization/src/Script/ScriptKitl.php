<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptKitl - Representation of the Khitan large script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptKitl extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Kitl';
    }

    public function number()
    {
        return '505';
    }
}
