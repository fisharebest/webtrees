<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptLatn;

/**
 * Class LocaleSrLatn
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSrLatn extends LocaleSr
{
    public function endonym()
    {
        return 'srpski';
    }

    public function endonymSortable()
    {
        return 'SRPSKI';
    }

    public function script()
    {
        return new ScriptLatn();
    }
}
