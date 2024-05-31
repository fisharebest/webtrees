<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptLatn;

/**
 * Class LocaleCeLatn - Chechen
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleCeLatn extends LocaleCe
{
    public function endonym()
    {
        return 'Chechen';
    }

    public function endonymSortable()
    {
        return 'CHECHEN';
    }

    public function script()
    {
        return new ScriptLatn();
    }
}
