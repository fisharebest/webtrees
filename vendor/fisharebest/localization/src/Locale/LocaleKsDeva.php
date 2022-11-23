<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptDeva;

/**
 * Class LocaleKsDeva - Kashmiri
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleKsDeva extends LocaleKs
{
    public function direction()
    {
        return 'rtl';
    }

    public function endonym()
    {
        return 'कॉशुर';
    }

    public function script()
    {
        return new ScriptDeva();
    }
}
