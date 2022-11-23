<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptGlag;

/**
 * Class LocaleCuGlag - Old Church Slavonic
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleCuGlag extends LocaleCu
{
    public function endonym()
    {
        return 'ⰔⰎⰑⰂⰡⰐⰠⰔⰍⰟ';
    }

    public function endonymSortable()
    {
        return 'ⰔⰎⰑⰂⰡⰐⰠⰔⰍⰟ';
    }

    public function script()
    {
        return new ScriptGlag();
    }
}
