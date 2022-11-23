<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMarc - Representation of the Marchen script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptMarc extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Marc';
    }

    public function number()
    {
        return '332';
    }

    public function unicodeName()
    {
        return 'Marchen';
    }
}
