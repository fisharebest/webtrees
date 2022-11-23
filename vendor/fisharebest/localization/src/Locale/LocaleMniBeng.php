<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptBeng;

/**
 * Class LocaleMni - Meitei
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleMniBeng extends LocaleMni
{
    public function endonym()
    {
        return 'মৈতৈলোন্';
    }

    public function script()
    {
        return new ScriptBeng();
    }
}
