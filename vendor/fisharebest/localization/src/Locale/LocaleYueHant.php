<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptHant;

/**
 * Class LocaleYueHant - Yue
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocaleYueHant extends LocaleYue
{
    public function endonym()
    {
        return '粤语';
    }

    public function script()
    {
        return new ScriptHant();
    }
}
