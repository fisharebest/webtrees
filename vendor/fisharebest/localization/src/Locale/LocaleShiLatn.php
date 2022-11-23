<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptLatn;

/**
 * Class LocaleShiLatn
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleShiLatn extends LocaleShi
{
    public function endonym()
    {
        return 'tamazight';
    }

    public function endonymSortable()
    {
        return 'TAMAZIGHT';
    }

    public function script()
    {
        return new ScriptLatn();
    }
}
