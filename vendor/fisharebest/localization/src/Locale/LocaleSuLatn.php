<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptLatn;

/**
 * Class LocaleSuLatn
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSuLatn extends LocaleSu
{
    public function endonym()
    {
        return 'Basa Sunda';
    }

    public function endonymSortable()
    {
        return 'BASA SUNDA';
    }

    public function script()
    {
        return new ScriptLatn();
    }
}
