<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptPhlv - Representation of the Book Pahlavi script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptPhlv extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Phlv';
    }

    public function number()
    {
        return '133';
    }
}
