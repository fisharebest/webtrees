<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptLatn;

/**
 * Class LocaleJv - Javanese
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocaleJvLatn extends LocaleJv
{
    public function endonym()
    {
        return 'Basa Jawa';
    }

    public function endonymSortable()
    {
        return 'BASA JAWA';
    }

    public function script()
    {
        return new ScriptLatn();
    }
}
