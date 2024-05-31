<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptLatn;

/**
 * Class LocaleIuLatn - Inuktitut
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleIuLatn extends LocaleIu
{
    public function endonym()
    {
        return 'Inuktitut';
    }

    public function endonymSortable()
    {
        return 'INUKTITUT';
    }

    public function script()
    {
        return new ScriptLatn();
    }
}
