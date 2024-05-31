<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSind - Representation of the Khudawadi, Sindhi script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptSind extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Sind';
    }

    public function number()
    {
        return '318';
    }

    public function unicodeName()
    {
        return 'Khudawadi';
    }
}
